<?php
/**
 * Articles
 *
 * Copyright 2011-12 by Shaun McCormick <shaun+articles@modx.com>
 *
 * Articles is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Articles is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Articles; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package articles
 */
/**
 * @package articles
 * @subpackage processors
 */
class ArticleGetListProcessor extends modObjectGetListProcessor {
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
        $action = $this->modx->getObject(modAction::class, [
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
            if ($this->modx->getOption('commentsEnabled',$settings,true)) {
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

    public function prepareQueryBeforeCount(xPDOQuery $c) {
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

    public function prepareQueryAfterCount(xPDOQuery $c) {
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
     * @param xPDOObject|Article $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $resourceArray = parent::prepareRow($object);

        if (!empty($resourceArray['publishedon'])) {
            $resourceArray['publishedon_date'] = date($this->modx->getOption('articles.mgr_date_format',null,"M d"),$resourceArray['publishedon']);
            $resourceArray['publishedon_time'] = date($this->modx->getOption('articles.mgr_time_format',null,"H:i:s"),$resourceArray['publishedon']);
            $resourceArray['publishedon'] = date("Y-m-d H:i:s",$resourceArray['publishedon']);
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
		    $string = mb_substr($string,0,$length,$this->modx->config['charset']).'...';
	    }
        return $string;
    }
}
return ArticleGetListProcessor::class;
