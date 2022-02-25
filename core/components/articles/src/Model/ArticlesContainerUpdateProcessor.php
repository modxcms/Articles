<?php

namespace Articles\Model;

use MODX\Revolution\modSystemSetting;
use MODX\Revolution\Processors\Resource\Update;

/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the Articles type
 *
 * @package articles
 */
class ArticlesContainerUpdateProcessor extends Update {
    /** @var ArticlesContainer $object */
    public $object;

    public function initialize()
    {
        $initialize = parent::initialize();
        $properties = $this->getProperties();
        $properties['class_key'] = ArticlesContainer::class;
        $this->setProperties($properties);
        return $initialize;
    }

    /**
     * Override modResourceUpdateProcessor::beforeSave to provide custom functionality, saving settings for the container
     * to a custom field in the DB
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeSave() {
        $properties = $this->getProperties();
        $settings = $this->object->getProperties('articles');
        $notificationServices = [];
        foreach ($properties as $k => $v) {
            if (substr($k,0,8) == 'setting_') {
                $key = substr($k,8);
                if ($v === 'false') $v = 0;
                if ($v === 'true') $v = 1;

                switch ($key) {
                    case 'notifyTwitter':
                        if ($v) $notificationServices[] = 'twitter';
                        break;
                    case 'notifyTwitterConsumerKey':
                        if (!empty($v)) {
                            $v = $this->object->encrypt($v);
                        }
                        break;
                    case 'notifyTwitterConsumerKeySecret':
                        if (!empty($v)) {
                            $v = $this->object->encrypt($v);
                        }
                        break;
                    case 'notifyFacebook':
                        if ($v) $notificationServices[] = 'facebook';
                        break;
                }
                $settings[$key] = $v;
            }
        }
        $settings['notificationServices'] = implode(',',$notificationServices);
        $this->object->setProperties($settings,'articles');
        return parent::beforeSave();
    }

    /**
     * Override modResourceUpdateProcessor::afterSave to provide custom functionality
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        $this->addContainerId();
        $this->removeFromArchivistIds();
        $this->setProperty('clearCache',true);
        //$this->object->set('isfolder',true);
        return parent::afterSave();
    }

    /**
     * Add the Container ID to the articles system setting for managing IDs for FURL redirection.
     * @return boolean
     */
    public function addContainerId() {
        $saved = true;
        /** @var modSystemSetting $setting */
        $setting = $this->modx->getObject(modSystemSetting::class, ['key' => 'articles.container_ids']);
        if (!$setting) {
            $setting = $this->modx->newObject(modSystemSetting::class);
            $setting->set('key','articles.container_ids');
            $setting->set('namespace','articles');
            $setting->set('area','furls');
            $setting->set('xtype','textfield');
        }
        $value = $setting->get('value');
        $archiveKey = $this->object->get('id').':arc_';
        $value = is_array($value) ? $value : explode(',',$value);
        if (!in_array($archiveKey,$value)) {
            $value[] = $archiveKey;
            $value = array_unique($value);
            $setting->set('value',implode(',',$value));
            $saved = $setting->save();
        }
        return $saved;
    }

    /**
     * Remove from Archivist IDs on prior versions of Archivist, to prevent conflicts
     * @return boolean
     */
    public function removeFromArchivistIds() {
        $saved = true;
        /** @var modSystemSetting $setting */
        $setting = $this->modx->getObject(modSystemSetting::class, ['key' => 'archivist.archive_ids']);
        if ($setting) {
            $value = $setting->get('value');
            $archiveKey = $this->object->get('id').':arc_';
            $value = is_array($value) ? $value : explode(',',$value);
            if (in_array($archiveKey,$value)) {
                $newKeys = [];
                foreach ($value as $k => $v) {
                    if ($v == $archiveKey) continue;
                    $newKeys[] = $v;
                }
                $newKeys = array_unique($newKeys);
                $setting->set('value',implode(',',$newKeys));
                $saved = $setting->save();
            }
        }
        return $saved;
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