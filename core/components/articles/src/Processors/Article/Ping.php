<?php

namespace Articles\Processors\Article;

use Articles\Model\Article;
use MODX\Revolution\Processors\ModelProcessor;

class Ping extends ModelProcessor {
    public $classKey = Article::class;
    public $objectType = 'article';
    public $languageTopics = ['resource','articles:default'];
    /** @var Article $object */
    public $object;

    public function initialize() {
        $initialized = parent::initialize();
        $id = $this->getProperty('id',null);
        if (empty($id)) { return $this->modx->lexicon('articles.articles_err_ns'); }
        $this->object = $this->modx->getObject(Article::class,$id);
        if (empty($this->object)) return $this->modx->lexicon('articles.article_err_nf');
        return $initialized;
    }
    public function process() {
        if ($this->object->notifyUpdateServices()) {
            return $this->success();
        } else {
            return $this->failure();
        }
    }
}
return Ping::class;