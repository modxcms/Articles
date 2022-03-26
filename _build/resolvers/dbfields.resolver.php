<?php

use MODX\Revolution\modX;
use Articles\Model\Article;
use Articles\Model\ArticlesContainer;
use xPDO\Transport\xPDOTransport;

/**
 * Handles updating class keys in the modResource table
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
            $modelPath = $modx->getOption('articles.core_path',null,$modx->getOption('core_path').'components/articles/').'src/';
            $modx->addPackage('Articles\Model', $modelPath, null, 'Articles\\');

            // Look for old class names in the database and update them
            $oldClassKeys = ['Article', 'ArticlesContainer'];
            $found = $modx->getCollection(modResource::class, [
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