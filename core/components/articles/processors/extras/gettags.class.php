<?php
/**
 * @package articles
 * @subpackage processors
 */
class ArticleExtrasGetTagsProcessor extends modObjectGetListProcessor {
    public $checkListPermission = true;


    public function process() {
        $container = $this->getProperty('container', false);
        if(!$container){
            return false;
        }

        $parent = $this->modx->getObject('modResource', $container);
        if(!$parent){
            return false;
        }

        $articles = $parent->getMany('Children',array('deleted' => 0));
        $articleIDs = array();
        foreach($articles as $article){
            $articleIDs[] = $article->id;
        }

        $templateVariable = $this->modx->getObject('modTemplateVar', array('name' => 'articlestags'));
        if(!$templateVariable){
            return false;
        }

        $c = $this->modx->newQuery('modTemplateVarResource');

        $valuesQuery = (boolean)$this->getProperty('valuesqry', false);
        if(!$valuesQuery){
            $query = $this->getProperty('query', '');
            if($query){
                $c->where(array(
                               'value:LIKE' => '%' . $query . '%',
                          ));
            }
        }

        $c->where(array(
                       'tmplvarid' => $templateVariable->id,
                       'contentid:IN' => $articleIDs
                  ));

        $tagsObject = $this->modx->getCollection('modTemplateVarResource', $c);
        $tags = array();

        foreach($tagsObject as $tagObject){
            $addTags = explode(',',$tagObject->value);
            foreach($addTags as &$addTag){
                $addTag = trim($addTag);
            }
            $tags = array_merge($tags, $addTags);
        }

        $tags = ArticlesService::arrayUnique($tags);

        $returnArray = array();
        foreach($tags as $tag){
            if(!$valuesQuery){
                if($query){
                    if (strpos($tag, $query) === FALSE) continue;
                }
            }
            $returnArray[] = array('tag' => $tag);
        }

        return $this->outputArray($returnArray, count($returnArray));
    }

}
return 'ArticleExtrasGetTagsProcessor';