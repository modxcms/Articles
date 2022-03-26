<?php

namespace Articles\Model;

use MODX\Revolution\modSystemSetting;
use MODX\Revolution\Processors\Resource\Create;

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the Articles type
 *
 * @package articles
 */
class ArticlesContainerCreateProcessor extends Create {
    /** @var ArticlesContainer $object */
    public $object;
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality, saving the container settings to a
     * custom field in the manager
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

        $this->object->set('class_key',ArticlesContainer::class);
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',true);
        return parent::beforeSave();
    }

    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        $this->addContainerId();
        $this->removeFromArchivistIds();
        $this->setProperty('clearCache',true);
        return parent::afterSave();
    }

    /**
     * Add the Container ID to the articles system setting for managing container IDs for FURL redirection.
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
}
