<?php

namespace Articles\Processors\Article;

use Articles\Model\Article;
use Articles\Model\ArticlesContainer;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modUser;
use MODX\Revolution\Processors\Model\GetListProcessor;
use quipComment;
use quipThread;

/**
 * @package articles
 * @subpackage processors
 */
class GetList extends GetListProcessor {
    public $classKey = Article::class;
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $checkListPermission = true;
    public $objectType = 'article';
    public $languageTopics = ['resource','articles:default'];

    /** @var int|string $editAction */
    public $editAction;
    /** @var modTemplateVar $tvTags */
    public $tvTags;
    /** @var ArticlesContainer $container */
    public $container;
    /** @var boolean $commentsEnabled */
    public $commentsEnabled = false;

    public function initialize() {

        // @todo: we need an alternative to modAction as it no longer exists!

        $action = $this->modx->getObject('modAction', [
            'namespace' => 'core',
            'controller' => 'resource/update',
        ]);
        if ($action) {
            $this->editAction = $action->get('id');
        }
        else {
            $this->editAction = 'resource/update';
        }
        $this->defaultSortField = $this->modx->getOption('articles.default_article_sort_field',null,'createdon');

        if ($this->getParentContainer()) {
            $settings = $this->container->getContainerSettings();
            if ($this->modx->getOption('commentsEnabled',$settings,false)) {
                $quipCorePath = $this->modx->getOption('quip.core_path',null,$this->modx->getOption('core_path',null,MODX_CORE_PATH).'components/quip/');
                if ($this->modx->addPackage('quip',$quipCorePath.'model/')) {
                    $this->commentsEnabled = true;
                }
            }
        }
        return parent::initialize();
    }

    public function getTagsTV() {
        $this->tvTags = $this->modx->getObject(modTemplateVar::class, ['name' => 'articlestags']);
        if (!$this->tvTags && $this->getProperty('sort') == 'tags') {
            $this->setProperty('sort','createdon');
        }
        return $this->tvTags;
    }

    public function getParentContainer() {
        $parent = $this->getProperty('parent');
        if (!empty($parent)) {
            $this->container = $this->modx->getObject(ArticlesContainer::class,$parent);
        }
        return $this->container;
    }

    public function prepareQueryBeforeCount(\xPDO\Om\xPDOQuery $c) {
        $c->leftJoin(modUser::class,'CreatedBy');

        if ($this->getTagsTV()) {
            $c->leftJoin(modTemplateVarResource::class,'Tags', [
                'Tags.tmplvarid' => $this->tvTags->get('id'),
                'Tags.contentid = Article.id',
            ]);
        }

        $parent = $this->getProperty('parent',null);
        if (!empty($parent)) {
            $c->where([
                'parent' => $parent,
            ]);
        }
        $query = $this->getProperty('query',null);
        if (!empty($query)) {
            $queryWhere = [
                'pagetitle:LIKE' => '%'.$query.'%',
                'OR:description:LIKE' => '%'.$query.'%',
                'OR:introtext:LIKE' => '%'.$query.'%',
            ];
            if ($this->tvTags) {
                $queryWhere['OR:Tags.value:LIKE'] = '%'.$query.'%';
            }
            $c->where($queryWhere);
        }
        $filter = $this->getProperty('filter','');
        switch ($filter) {
            case 'published':
                $c->where([
                    'published' => 1,
                    'deleted' => 0,
                ]);
                break;
            case 'unpublished':
                $c->where([
                    'published' => 0,
                    'deleted' => 0,
                ]);
                break;
            case 'deleted':
                $c->where([
                    'deleted' => 1,
                ]);
                break;
            default:
                $c->where([
                    'deleted' => 0,
                ]);
                break;
        }

        $c->where([
            'class_key' => Article::class,
        ]);
        return $c;
    }

    public function getSortClassKey() {
        $classKey = Article::class;
        switch ($this->getProperty('sort')) {
            case 'tags':
                $classKey = modTemplateVarResource::class;
                break;
        }
        return $classKey;
    }

    public function prepareQueryAfterCount(\xPDO\Om\xPDOQuery $c) {
        $c->select($this->modx->getSelectColumns(Article::class,'Article'));
        $c->select([
            'createdby_username' => 'CreatedBy.username',
        ]);
        if ($this->tvTags) {
            $c->select([
                'tags' => 'Tags.value',
            ]);
        }
        if ($this->commentsEnabled) {
            $commentsQuery = $this->modx->newQuery(quipComment::class);
            $commentsQuery->innerJoin(quipThread::class,'Thread');
            $commentsQuery->where([
                'Thread.resource = Article.id',
            ]);
            $commentsQuery->select([
                'COUNT('.$this->modx->getSelectColumns(quipComment::class,'quipComment','', ['id']).')',
            ]);
            $commentsQuery->construct();
            $c->select([
                '('.$commentsQuery->toSQL().') AS '.$this->modx->escape('comments'),
            ]);
        }
        return $c;
    }

    /**
     * @param \xPDO\Om\xPDOObject|Article $object
     * @return array
     */
    public function prepareRow(\xPDO\Om\xPDOObject $object) {
        $resourceArray = parent::prepareRow($object);

        if (!empty($resourceArray['publishedon'])) {
        	$publishedon = strtotime($resourceArray['publishedon']);
            $resourceArray['publishedon_date'] = strftime($this->modx->getOption('articles.mgr_date_format',null,'%b %d'),$publishedon);
            $resourceArray['publishedon_time'] = strftime($this->modx->getOption('articles.mgr_time_format',null,'%H:%I %p'),$publishedon);
            $resourceArray['publishedon'] = strftime('%b %d, %Y %H:%I %p',$publishedon);
        }
        $resourceArray['action_edit'] = '?a='.$this->editAction.'&action=post/update&id='.$resourceArray['id'];
        if (!array_key_exists('comments',$resourceArray)) $resourceArray['comments'] = 0;

        $this->modx->getContext($resourceArray['context_key']);
        $resourceArray['preview_url'] = $this->modx->makeUrl($resourceArray['id'],$resourceArray['context_key']);

        $trimLength = $this->modx->getOption('articles.mgr_article_content_preview_length',null,300);
        $resourceArray['content'] = strip_tags($this->ellipsis($object->getContent(),$trimLength));

        $resourceArray['actions'] = [];
        $resourceArray['actions'][] = [
            'className' => 'edit',
            'text' => $this->modx->lexicon('edit'),
        ];
        $resourceArray['actions'][] = [
            'className' => 'view',
            'text' => $this->modx->lexicon('view'),
        ];
        if (!empty($resourceArray['deleted'])) {
            $resourceArray['actions'][] = [
                'className' => 'undelete',
                'text' => $this->modx->lexicon('undelete'),
            ];
        } else {
            $resourceArray['actions'][] = [
                'className' => 'delete',
                'text' => $this->modx->lexicon('delete'),
            ];
        }
        if (!empty($resourceArray['published'])) {
            $resourceArray['actions'][] = [
                'className' => 'unpublish',
                'text' => $this->modx->lexicon('unpublish'),
            ];
        } else {
            $resourceArray['actions'][] = [
                'className' => 'publish orange',
                'text' => $this->modx->lexicon('publish'),
            ];
        }
        return $resourceArray;
    }

    public function ellipsis($string,$length = 300) {
	    if (mb_strlen($string) > $length) {
            $encoding = $this->modx->getOption('modx_charset',null,'UTF-8');
            $string = mb_substr($string,0,$length,$encoding).'...';
	    }
        return $string;
    }
}
return GetList::class;
