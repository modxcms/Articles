<?php

namespace Articles\Model\Import;

use Articles\Model\Article;
use Articles\Model\ArticlesContainer;
use MODX\Revolution\modUser;
use MODX\Revolution\modUserProfile;
use quipComment;
use quipThread;
use SimpleXMLElement;

/**
 * @package articles
 * @subpackage import
 */
class ArticlesImportBlogger extends ArticlesImport {
    /** @var ArticlesContainer $container */
    public $container;

    public $hrefMap = [];
    public $commentMap = [];

    public function import() {
        /** @var SimpleXMLElement $data */
        $data = $this->getData();
        if (empty($data)) return false;

        $this->createContainer();
        if (empty($this->container)) {
            $this->addError('blogger-file',$this->modx->lexicon('articles.import_blogger_container_err_nf'));
            return false;
        }

        $imported = false;
        foreach ($data->entry as $entry) {
            $dc = $entry->children('thr',true);
            if ($dc->total) {
                $article = $this->createArticle($entry);
                if (!empty($article)) {
                    $imported = true;
                }
            } else if ($dc->{'in-reply-to'}) {
                $this->importComment($entry);
            }
        }
        return $imported;
    }

    /**
     * Get the parsed XML
     * @return bool|SimpleXMLElement
     */
    public function getData() {
        if (empty($_FILES['blogger-file']) || !empty($_FILES['blogger-file']['error'])) {
            $file = isset($this->config['blogger-file-server']) ? $this->config['blogger-file-server'] : '';
            if (empty($file)) {
                $this->addError('blogger-file-server',$this->modx->lexicon('articles.import_blogger_file_err_nf'));
                return false;
            }
            $file = str_replace([
                '{core_path}',
                '{base_path}',
                '{assets_path}',
            ], [
                $this->modx->getOption('core_path',null,MODX_CORE_PATH),
                $this->modx->getOption('base_path',null,MODX_BASE_PATH),
                $this->modx->getOption('assets_path',null,MODX_ASSETS_PATH),
            ],$file);
            if (!file_exists($file)) {
                $this->processor->addFieldError('blogger-file-server',$this->modx->lexicon('articles.import_blogger_file_err_nf'));
                return false;
            }
        } else {
            $file = isset($_FILES['blogger-file']) ? $_FILES['blogger-file'] : '';
            if (empty($file) || !file_exists($file['tmp_name'])) {
                return false;
            }
            $file = $file['tmp_name'];
            if (!file_exists($file)) {
                $this->processor->addFieldError('blogger-file-server',$this->modx->lexicon('articles.import_blogger_file_err_nf'));
            }
        }
        $contents = file_get_contents($file);
        $xml = @simplexml_load_string($contents,'SimpleXMLElement',LIBXML_NOCDATA);
        return $xml;
    }


    /**
     * Create or select the container
     *
     * @return ArticlesContainer
     */
    public function createContainer() {
        if (!empty($this->config['id'])) {
            $this->container = $this->modx->getObject(ArticlesContainer::class,$this->config['id']);
        } else {
            /* @TODO Finish ability to import into new blog. */
            $this->container = $this->modx->newObject(ArticlesContainer::class);
            $this->container->fromArray([
                'parent' => $this->modx->getOption('parent',$this->config,0),
            ]);
        }
        return $this->container;
    }

    /**
     * Create the article
     * @param SimpleXMLElement $entry
     * @return Article|boolean
     */
    public function createArticle(SimpleXMLElement $entry) {
        $settings = $this->container->getContainerSettings();
        /** @var Article $article */
        $article = $this->modx->newObject(Article::class);

        $creator = $this->matchCreator((string)$entry->author->name);
        if (empty($creator)) {
            $creator = $this->matchCreator((string)$entry->author->email,1,'email');
        }

        $article->fromArray([
            'parent' => $this->container->get('id'),
            'pagetitle' => (string)$entry->title,
            'description' => '',
            'template' => $this->modx->getOption('articleTemplate',$settings,0),
            'published' => $this->parsePublished($entry),
            'publishedon' => strtotime((string)$entry->published),
            'publishedby' => $creator,
            'createdby' => $creator,
            'createdon' => strtotime((string)$entry->updated),
            'updatedon' => strtotime((string)$entry->updated),

            'content' => (string)$entry->content,
            'introtext' => '',
            'show_in_tree' => false,
            'class_key' => Article::class,
            'context_key' => $this->container->get('context_key'),
        ]);
        $article->setProperties($settings,'articles');

        if (!$this->debug) {
            $article->save();
        }

        /* have to do this after to get ID for href map */
        $alias = $this->parseAlias($entry,$article);
        $article->set('alias',$alias);
        $article->setArchiveUri();
        if (!$this->debug) {
            $article->save();
        }

        $this->importTags($article,$entry);
        return $article;
    }

    public function parsePublished(SimpleXMLElement $entry) {
        $dc = $entry->children('app',true);
        return $dc->control->draft != 'yes';
    }

    public function parseAlias(SimpleXMLElement $entry,Article $article) {
        $alias = '';
        foreach ($entry->link as $link) {
            if (!empty($link['rel']) && $link['rel'] == 'alternate') {
                $url = (string)$link['href'];

                $this->hrefMap[$url] = !$this->debug ? $article->get('id') : rand(1,10000);

                $lastSlash = strpos(strrev($url),'/');
                $strLength = strlen($url);
                $lastPos = $strLength - $lastSlash;
                $alias = substr($url,$lastPos);
                $ext = pathinfo($alias,PATHINFO_EXTENSION);
                $alias = str_replace(['.',$ext],'',$alias);
            }
        }
        if (empty($alias)) {
            $title = (string)$entry->title;
            $alias = $article->cleanAlias($title);
        }
        return $alias;
    }

    /**
     * Get the XPath string for the XML element
     * @param SimpleXMLElement $item
     * @param string $path
     * @return SimpleXMLElement|SimpleXMLElement[]
     */
    public function getXPath(SimpleXMLElement $item,$path) {
        $data = $item->xpath($path.'/text()');
        return array_key_exists(0,$data) ? $data[0] : $data;
    }

    /**
     * See if we can find a matching user for the comment/post
     * @param string $match
     * @param int $default
     * @param string $field
     * @return int|mixed
     */
    public function matchCreator($match,$default = 0,$field = 'username') {
        /** @var modUser $user */
        $c = $this->modx->newQuery(modUser::class);
        $c->innerJoin(modUserProfile::class,'Profile');
        $fieldAlias = 'modUser';
        if (!in_array($field, ['username','id'])) $fieldAlias = 'Profile';
        $c->where([
            $fieldAlias.'.'.$field => $match,
        ]);
        $user = $this->modx->getObject(modUser::class,$c);
        if ($user) {
            return $user->get('id');
        }
        return $default;
    }

    /**
     * Import comments into Quip
     *
     * @param SimpleXMLElement $entry
     * @return boolean
     */
    public function importComment(SimpleXMLElement $entry) {
        $settings = $this->container->getContainerSettings();
        $dc = $entry->children('thr',true)->attributes();
        $url = (string)$dc['href'];

        if (empty($this->hrefMap[$url])) {
            return false;
        }
        $articleId = $this->hrefMap[$url];

        if ($this->debug) {
            $article = $this->modx->newObject(Article::class);
            $article->set('id',1);
        } else {
            /** @var Article $article */
            $article = $this->modx->getObject(Article::class,$articleId);
            if (empty($article)) return false;
        }

        $threadKey = 'article-b'.$this->container->get('id').'-'.$article->get('id');

        /** @var quipThread $thread */
        $thread = $this->modx->newObject(quipThread::class);
        $thread->fromArray([
            'createdon' => $article->get('publishedon'),
            'moderated' => $this->modx->getOption('commentsModerated',$settings,1),
            'moderator_group' => $this->modx->getOption('commentsModeratorGroup',$settings,'Administrator'),
            'moderators' => $this->modx->getOption('commentsModerators',$settings,''),
            'resource' => $article->get('id'),
            'idprefix' => 'qcom',
        ]);
        $thread->set('name',$threadKey);
        if (!$this->debug) {
            $thread->save();
        }

        $email = (string)$entry->author->email;
        if ($email == 'noreply@blogger.com') $email = '';
        $commentId = (string)$entry->id;
        $creator = $this->matchCreator((string)$entry->author->name);
        if (empty($creator) && !empty($email)) {
            $creator = $this->matchCreator($email,1,'email');
        }

        /** @var quipComment $comment */
        $comment = $this->modx->newObject(quipComment::class);
        $comment->fromArray([
            'thread' => $threadKey,
            'parent' => 0,
            'author' => $creator,
            'body' => (string)$entry->content,
            'createdon' => strftime('%Y-%m-%d %H:%M:%S',strtotime((string)$entry->published)),
            'approved' => strftime('%Y-%m-%d %H:%M:%S',strtotime((string)$entry->published)),
            'name' => (string)$entry->author->name,
            'email' => $email,
            'website' => (string)$entry->author->uri,
            'ip' => '',
            'resource' => $article->get('id'),
            'idprefix' => 'qcom',
        ],'',true);

        $this->commentMap[$commentId] = $comment->get('id');

        if (!$this->debug) {
            $comment->save();
        }

        return true;
    }

    /**
     * Import any WP post meta tags
     * @param Article $article
     * @param SimpleXMLElement $entry
     * @return array
     */
    public function importTags(Article $article,SimpleXMLElement $entry) {
        $tags = [];
        if (empty($entry->category)) return;

        foreach ($entry->category as $category) {
            if (empty($category['scheme']) || $category['scheme'] != 'http://www.blogger.com/atom/ns#') continue;
            $tags[] = $category['term'];
        }
        if (!$this->debug) {
            $article->setTVValue('articlestags',implode(',',$tags));
        }
        return $tags;
    }
}