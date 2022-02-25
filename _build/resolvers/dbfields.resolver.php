<?php

use Articles\Model\Article;
use Articles\Model\ArticlesContainer;
use MODX\Revolution\modSystemSetting;
use xPDO\Transport\xPDOTransport;

/**
 * Handles adding custom fields to modResource table
 *
 * @var xPDOObject $object
 * @var array $options
 * @package articles
 * @subpackage build
 */
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;
//            $modelPath = $modx->getOption('articles.core_path',null,$modx->getOption('core_path').'components/articles/').'model/';
//            $modx->addPackage('articles',$modelPath);

            $articles = $modx->services->get('articles');

            /** @var xPDOManager $manager */
            $manager = $modx->getManager();

            /** @var modSystemSetting $setting */
            $setting = $modx->getObject(modSystemSetting::class, ['key' => 'articles.properties_migration']);
            if (!$setting || $setting->get('value') == false) {
                $c = $modx->newQuery(ArticlesContainer::class);
                $c->select([
                    'id',
                    'articles_container_settings',
                ]);
                $c->where([
                    'class_key' => ArticlesContainer::class,
                ]);
                $c->construct();
                $sql = $c->toSql();
                $stmt = $modx->query($sql);
                if ($stmt) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $settings = $row['articles_container_settings'];
                        $settings = is_array($settings) ? $settings : $modx->fromJSON($settings);
                        $settings = !empty($settings) ? $settings : [];
                        /** @var modResource $resource */
                        $resource = $modx->getObject(modResource::class,$row['id']);
                        if ($resource) {
                            $resource->setProperties($settings,'articles');
                            $resource->save();
                        }
                    }
                    $stmt->closeCursor();
                }
                $manager->removeField(Article::class,'articles_container');
                $manager->removeField(Article::class,'articles_container_settings');
                if (!$setting) {
                    $setting = $modx->newObject(modSystemSetting::class);
                    $setting->set('key','articles.properties_migration');
                    $setting->set('xtype','combo-boolean');
                    $setting->set('namespace','articles');
                    $setting->set('area','system');
                }
                $setting->set('value',true);
                $setting->save();
            }

            // Look for old class names in the database and update them
            $oldClassKeys = ['Article', 'ArticlesContainer'];
            $found = $this->modx->getCollection(modResource::class, [
                'class_key:IN' => $oldClassKeys
            ]);
            foreach ($found as $resource) {
                switch ($resource->get('class_key')) {
                    case 'Article':
                        $resource->set('class_key', Article::class);
                        $resource->save();
                        break;
                    case 'ArticlesContainer':
                        $resource->set('class_key', ArticlesContainer::class);
                        $resource->save();
                        break;
                }
            }
            break;
    }
}
return true;