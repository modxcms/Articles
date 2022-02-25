<?php

namespace Articles\Model;

use MODX\Revolution\modResource;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modX;
use MODX\Revolution\Processors\Resource\Create;

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the Article type
 *
 * @package articles
 */
class ArticleCreateProcessor extends Create {
    /** @var modResource $yearParent */
    public $yearParent;
    /** @var modResource $monthParent */
    public $monthParent;
    /** @var modResource $dayParent */
    public $dayParent;
    /** @var Article $object */
    public $object;
    /** @var boolean $isPublishing */
    public $isPublishing = false;

    public function beforeSet()
    {
        $this->setProperty('searchable',true);
        $this->setProperty('richtext',true);
        $this->setProperty('isfolder',false);
        $this->setProperty('cacheable',true);
        $this->setProperty('clearCache',true);
        $this->setProperty('class_key',Article::class);
        return parent::beforeSet();
    }

    /**
     * Override modResourceCreateProcessor::beforeSave to provide archiving
     *
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeSave()
    {
        $beforeSave = parent::beforeSave();

        if (!$this->parentResource) {
            $this->parentResource = $this->object->getOne('Parent');
        }

        if ($this->object->get('published') || $this->object->get('pub_date')) {
            if (!$this->setArchiveUri()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,'Failed to set URI for new Article.');
            }
        }

        /** @var ArticlesContainer $container */
        $container = $this->modx->getObject(ArticlesContainer::class,$this->object->get('parent'));
        if ($container) {
            $settings = $container->getProperties('articles');
            $this->object->setProperties($settings,'articles');
            $this->object->set('richtext',!isset($settings['articlesRichtext']) || !empty($settings['articlesRichtext']));
        }

        $this->isPublishing = $this->object->isDirty('published') && $this->object->get('published');
        return $beforeSave;
    }

    /**
     * Set the friendly URL archive by forcing it into the URI.
     * @return bool|string
     */
    public function setArchiveUri()
    {
        if (!$this->parentResource) {
            return false;
        }
        return $this->object->setArchiveUri();
    }

    public function afterSave() {
        $afterSave = parent::afterSave();
        $this->saveTemplateVariables();
        if($this->object->get('clearCache')) $this->clearContainerCache();
        if ($this->isPublishing) {
            $this->object->notifyUpdateServices();
            $this->object->sendNotifications();
        }
        return $afterSave;
    }

    /**
     * Clears the container cache to ensure that the container listing is updated
     * @return void
     */
    public function clearContainerCache()
    {
        $this->modx->cacheManager->refresh([
            'db' => [],
            'auto_publish' => ['contexts' => [$this->object->get('context_key')]],
            'context_settings' => ['contexts' => [$this->object->get('context_key')]],
            'resource' => ['contexts' => [$this->object->get('context_key')]],
        ]);
    }

    /**
     * Extend the saveTemplateVariables method and provide handling for the 'tags' type to store in a hidden TV
     * @return array|mixed
     */
    public function saveTemplateVariables()
    {
        $tags = $this->getProperty('tags',null);
        if ($tags !== null) {
            /** @var modTemplateVar $tv */
            $tv = $this->modx->getObject(modTemplateVar::class, [
                'name' => 'articlestags',
            ]);
            if ($tv) {
                $defaultValue = $tv->processBindings($tv->get('default_text'),$this->object->get('id'));
                if (strcmp($tags,$defaultValue) != 0) {
                    /* update the existing record */
                    $tvc = $this->modx->getObject(modTemplateVarResource::class, [
                        'tmplvarid' => $tv->get('id'),
                        'contentid' => $this->object->get('id'),
                    ]);
                    if ($tvc == null) {
                        /** @var modTemplateVarResource $tvc add a new record */
                        $tvc = $this->modx->newObject(modTemplateVarResource::class);
                        $tvc->set('tmplvarid',$tv->get('id'));
                        $tvc->set('contentid',$this->object->get('id'));
                    }
                    $tvc->set('value',$tags);
                    $tvc->save();

                    /* if equal to default value, erase TVR record */
                } else {
                    $tvc = $this->modx->getObject(modTemplateVarResource::class, [
                        'tmplvarid' => $tv->get('id'),
                        'contentid' => $this->object->get('id'),
                    ]);
                    if (!empty($tvc)) {
                        $tvc->remove();
                    }
                }
            }
        }
        return true;
    }
}
