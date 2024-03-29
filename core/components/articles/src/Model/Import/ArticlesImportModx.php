<?php

namespace Articles\Model\Import;

use Articles\Model\Article;
use Articles\Model\ArticlesContainer;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modX;
use MODX\Revolution\mysql\modResource;
use quipThread;
use xPDO\Om\xPDOQuery;

/**
 * @package articles
 * @subpackage import
 */
class ArticlesImportMODX extends ArticlesImport {
    /** @var ArticlesContainer $container */
    public $container;

    public function import() {
        $imported = false;
        $this->container = $this->modx->getObject(ArticlesContainer::class,$this->config['id']);

        $c = $this->getQuery();
        if ($c === false) return $imported;

        $resources = $this->modx->getIterator(modResource::class,$c);
        if (empty($resources)) {
            $this->processor->addFieldError('parents','No resources found!');
            return false;
        }
        foreach ($resources as $resource) {
            $imported = $this->importResource($resource);
        }
        return $imported;
    }

    public function getQuery() {
        $c = $this->modx->newQuery(modResource::class);
        $c->select($this->modx->getSelectColumns(modResource::class,'modResource'));
        $where = [];

        /* parents */
        $ids = [];
        if (!empty($this->config['modx-parents'])) {
            $parents = is_array($this->config['modx-parents']) ? $this->config['modx-parents'] : explode(',',$this->config['modx-parents']);
            foreach ($parents as $parent) {
                /** @var modResource $parentResource */
                $parentResource = $this->modx->getObject(modResource::class,$parent);
                if (!$parentResource) continue;

                $children = $this->modx->getChildIds($parent,10, [
                    'context' => $parentResource->get('context_key'),
                ]);
                $ids = array_merge($ids,$children);
            }
        }

        /* specific resources */
        $exclude = [];
        $include = [];
        if (!empty($this->config['modx-resources'])) {
            $resources = is_array($this->config['modx-resources']) ? $this->config['modx-resources'] : explode(',',$this->config['modx-resources']);
            foreach ($resources as $resourceId) {
                if (strpos($resourceId,'-') === 0) {
                    $exclude[] = intval(substr($resourceId,1));
                } else {
                    $include[] = intval($resourceId);
                }
            }
        }
        $ids = array_merge($ids,$include);
        sort($ids);
        $ids = array_unique($ids);
        if (!empty($ids)) {
            $where['id:IN'] = $ids;
        }

        $exclude = array_unique($exclude);
        if (!empty($exclude)) {
            $where['id:NOT IN'] = $exclude;
        }

        /* template */
        if (!empty($this->config['modx-template'])) {
            $where['template'] = $this->config['modx-template'];
        }

        if (isset($this->config['modx-unpublished']) && empty($this->config['modx-unpublished'])) {
            $where['published'] = 1;
        }

        if (isset($this->config['modx-hidemenu']) && empty($this->config['modx-hidemenu'])) {
            $where['hidemenu'] = 0;
        }

        if (empty($where)) {
            $this->addError('modx-parents',$this->modx->lexicon('articles.import_modx_err_no_criteria'));
            return false;
        }

        /* dont let them get the site start */
        $where['id:!='] = [(int)$this->modx->getOption('site_start',null,1)];

        $where['isfolder'] = false;
        $where['class_key:!='] = Article::class;
        $c->where($where);

        if (!empty($this->config['modx-tagsField'])) {
            $this->getTagsQuery($c);
        }
        return $c;
    }

    /**
     * Get the nice little query to get the tags field
     * @param xPDOQuery $c
     */
    public function getTagsQuery(xPDOQuery &$c) {
        $tagsField = $this->config['modx-tagsField'];
        $isTV = true;
        if (intval($tagsField) > 0) {
            $tagsField = ['id' => $tagsField];
        } else {
            if (strpos($tagsField,'tv.') === 0) {
                $tagsField = ['name' => str_replace('tv.','',$tagsField)];
            } else {
                $isTV = false;
            }
        }

        if ($isTV) {
            /** @var modTemplateVar $tv */
            $tv = $this->modx->getObject(modTemplateVar::class,$tagsField);
            if ($tv) {
                $c->leftJoin(modTemplateVarResource::class,'Tags', [
                    'Tags.contentid = modResource.id',
                    'Tags.tmplvarid' => $tv->get('id'),
                ]);
                $c->select([
                    'tags' => 'Tags.value',
                ]);
            }
        } else {
            $c->select([
                'tags' => $tagsField,
            ]);
        }
    }

    /**
     * Import the Resource into Articles
     *
     * @param modResource $resource
     * @return boolean
     */
    public function importResource(modResource $resource) {
        $resource->set('searchable',true);
        $resource->set('richtext',true);
        $resource->set('isfolder',false);
        $resource->set('cacheable',true);
        $resource->set('class_key',Article::class);
        $resource->set('parent',$this->container->get('id'));
        $settings = $this->container->getProperties('articles');
        $resource->setProperties($settings,'articles');

        if (!empty($this->config['modx-change-template'])) {
            $resource->set('template',$settings['articleTemplate']);
        }

        $this->setResourceUri($resource);
        if (!empty($this->config['modx-commentsThreadNameFormat'])) {
            $this->importComments($resource);
        }

        $saved = true;
        if (!$this->debug) {
            $saved = $resource->save();
            if ($saved) {
                $resource->setTVValue('articlestags',$resource->get('tags'));
            }
        }

        return $saved;
    }

    /**
     * Set the new Articles-based URI
     * @param modResource $resource
     */
    public function setResourceUri(modResource &$resource) {
        $date = $resource->get('published') ? $resource->get('publishedon') : $resource->get('createdon');
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $day = date('d',strtotime($date));

        $containerUri = $this->container->get('uri');
        if (empty($containerUri)) {
            $containerUri = $this->container->get('alias');
        }
        $uri = rtrim($containerUri,$this->modx->getOption('container_suffix', null, '/')).'/'.$year.'/'.$month.'/'.$day.'/'.$resource->get('alias');

        $resource->set('uri',rtrim($uri,'/').'/');
        $resource->set('uri_override',true);
    }

    /**
     * If set, import any comments from Quip
     * @param modResource $resource
     * @return boolean
     */
    public function importComments(modResource &$resource) {
        $threadFormat = $this->config['modx-commentsThreadNameFormat'];
        if (empty($threadFormat)) return true;

        $imported = true;
        $threadFormat = str_replace(['[[*id]]','[[+id]]'],$resource->get('id'),$threadFormat);
        /** @var quipThread $thread */
        $thread = $this->modx->getObject(quipThread::class, ['name' => $threadFormat]);
        if ($thread) {
            $newThreadName = 'article-b'.$this->container->get('id').'-'.$resource->get('id');

            $sql = 'UPDATE '.$this->modx->getTableName(quipComment::class)
                 .' SET '.$this->modx->escape('thread').' = "'.$newThreadName.'"'
                 .' WHERE '.$this->modx->escape('thread').' = "'.$thread->get('name').'"';
            if (!$this->debug) {
                $this->modx->exec($sql);
            }

            $sql = 'UPDATE '.$this->modx->getTableName(quipThread::class)
                 .' SET '.$this->modx->escape('name').' = "'.$newThreadName.'"'
                 .' WHERE '.$this->modx->escape('name').' = "'.$thread->get('name').'"';
            if (!$this->debug) {
                $this->modx->exec($sql);
            }
            $imported = true;
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[Articles] Could not find Quip Thread with thread name: '.$threadFormat);
        }
        return $imported;
    }
}