<?php

namespace Articles\Processors\Article;

use Articles\Model\Article;
use modObjectProcessor;

/**
 * @package articles
 * @subpackage processors
 */
class DeleteMultiple extends modObjectProcessor {
    public $classKey = Article::class;
    public $objectType = 'article';
    public $languageTopics = ['resource','articles:default'];

    public function process() {
        $ids = $this->getProperty('ids',null);
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('articles.articles_err_ns_multiple'));
        }
        $ids = is_array($ids) ? $ids : explode(',',$ids);

        foreach ($ids as $id) {
            if (empty($id)) continue;
            $this->modx->runProcessor('resource/delete', [
                'id' => $id,
            ]);
        }
        return $this->success();
    }
}
return DeleteMultiple::class;