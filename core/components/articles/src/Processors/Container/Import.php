<?php

namespace Articles\Processors\Container;

use Articles\Model\Article;
use Articles\Model\ArticlesContainer;
use Articles\Model\Import\ArticlesImport;
use MODX\Revolution\Processors\ModelProcessor;

/**
 * @package articles
 * @subpackage processors
 */
class Import extends ModelProcessor {
    public $classKey = Article::class;
    public $objectType = 'article';
    public $languageTopics = ['resource','articles:default'];
    /** @var Article $object */
    public $object;
    /** @var ArticlesImport $service */
    public $service;

    public function initialize() {
        $initialized = parent::initialize();
        $id = $this->getProperty('id',null);
        if (empty($id)) { return $this->modx->lexicon('articles.container_err_ns'); }
        $this->object = $this->modx->getObject(ArticlesContainer::class,$id);
        if (empty($this->object)) return $this->modx->lexicon('articles.container_err_nf');
        return $initialized;
    }

    /**
     * Import data into Articles
     * {@inheritDoc}
     * @return array|string
     */
    public function process() {
        $this->getImportService();
        if (empty($this->service)) {
            return $this->failure('[Articles] Could not load import service!');
        }

        $success = $this->service->import();

        if ($success) {
            $this->clearCache();
            return $this->success();
        } else {
            return $this->failure();
        }
    }

    /**
     * Get the specified import service
     * @return ArticlesImport
     */
    public function getImportService() {
        $serviceName = $this->getProperty('service','WordPress');

        $modelPath = $this->modx->getOption('articles.core_path',null,$this->modx->getOption('core_path').'components/articles/') . 'src/Model/';
        $servicePath = $modelPath . 'Import/ArticlesImport' . ucfirst(strtolower($serviceName)) . '.php';
        if (file_exists($servicePath)) {
            require_once $servicePath;
            $className = ArticlesImport::class.$serviceName;
            $this->service = new $className(new \Articles\Articles($this->modx),$this,$this->getProperties());
        }

        return $this->service;
    }

    /**
     * Clear the site cache to properly refresh the URIs
     */
    public function clearCache() {
        $this->modx->cacheManager->refresh([
            'db' => [],
            'auto_publish' => ['contexts' => [$this->object->get('context_key')]],
            'context_settings' => ['contexts' => [$this->object->get('context_key')]],
            'resource' => ['contexts' => [$this->object->get('context_key')]],
        ]);
    }
}
return Import::class;