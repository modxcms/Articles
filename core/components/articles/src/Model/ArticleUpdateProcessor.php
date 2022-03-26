<?php

namespace Articles\Model;

use MODX\Revolution\modResource;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modX;
use MODX\Revolution\Processors\Resource\Update;

/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the Article type
 *
 * @package articles
 */
class ArticleUpdateProcessor extends Update {
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


    public function initialize()
    {
        $initialize = parent::initialize();
        $properties = $this->getProperties();
        $properties['class_key'] = Article::class;
        $this->setProperties($properties);
        return $initialize;
    }

    public function beforeSet() {
        $this->setProperty('clearCache',true);
        return parent::beforeSet();
    }

    /**
     * Override modResourceUpdateProcessor::beforeSave to provide archiving
     *
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeSave() {
        $afterSave = parent::beforeSave();
        $container = $this->modx->getObject(ArticlesContainer::class,$this->object->get('parent'));

        if ($this->object->get('published') && ($this->object->isDirty('alias') || $this->object->isDirty('published'))) {
            if (!$this->setArchiveUri()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,'Failed to set date URI.');
            }
        } else if (($this->object->get('pub_date') && $this->object->isDirty('pub_date')) || $this->object->isDirty('pub_date')) {
            if (!$this->setArchiveUri()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,'Failed to set date URI pub_date.');
            }
        } else if(!$this->object->get('published') && !$this->object->get('pub_date')) { // we need to always do this because the url may have been set previously by pub_date
            /*$containerUri = $container->get('uri');
            if (empty($containerUri)) {
                $containerUri = $container->get('alias');
            }*/
            $uri = rtrim($this->object->get('alias'));
            $this->object->set('uri',$uri);
            $this->object->set('uri_override',true);
        }

        /** @var ArticlesContainer $container */
        if ($container) {
            $this->object->setProperties($container->getProperties('articles'),'articles');
        }

        $this->isPublishing = $this->object->isDirty('published') && $this->object->get('published');
        return $afterSave;
    }

    /**
     * Extend the saveTemplateVariables method and provide handling for the 'tags' type to store in a hidden TV
     * @return array|mixed
     */
    public function saveTemplateVariables() {
        $saved = parent::saveTemplateVariables();
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
        return $saved;
    }

    /**
     * Set the friendly URL archive by forcing it into the URI.
     * @return bool|string
     */
    public function setArchiveUri() {
        if (!$this->parentResource) {
            $this->parentResource = $this->object->getOne('Parent');
            if (!$this->parentResource) {
                return false;
            }
        }

        return $this->object->setArchiveUri();
    }

    public function afterSave() {
        $afterSave = parent::afterSave();
        if ($this->isPublishing) {
            $this->object->notifyUpdateServices();
            $this->object->sendNotifications();
        }
        if($this->object->get('clearCache')) $this->clearContainerCache();
        return $afterSave;
    }

    /**
     * Clears the container cache to ensure that the container listing is updated
     * @return void
     */
    public function clearContainerCache() {
        $this->modx->cacheManager->refresh([
            'db' => [],
            'auto_publish' => ['contexts' => [$this->object->get('context_key')]],
            'context_settings' => ['contexts' => [$this->object->get('context_key')]],
            'resource' => ['contexts' => [$this->object->get('context_key')]],
        ]);
    }

    /**
     * Override cleanup to send only back needed params
     * @return array|string
     */
    public function cleanup() {
        $this->object->removeLock();
        $this->clearCache();

        $returnArray = $this->object->get(array_diff(array_keys($this->object->_fields), ['content','ta','introtext','description','link_attributes','pagetitle','longtitle','menutitle','articles_container_settings','properties']));
        foreach ($returnArray as $k => $v) {
            if (strpos($k,'tv') === 0) {
                unset($returnArray[$k]);
            }
            if (strpos($k,'setting_') === 0) {
                unset($returnArray[$k]);
            }
        }
        $returnArray['class_key'] = $this->object->get('class_key');
        $this->workingContext->prepare(true);
        $returnArray['preview_url'] = $this->modx->makeUrl($this->object->get('id'), $this->object->get('context_key'), '', 'full');
        return $this->success('',$returnArray);
    }
}