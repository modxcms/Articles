<?php

use xPDO\Transport\xPDOTransport;

/**
 * Handles adding Articles to Extension Packages
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
        case xPDOTransport::ACTION_UNINSTALL:
            $modx =& $object->xpdo;

            // This is unnecessary in MODX 3, so we'll just remove it.
            if ($modx instanceof modX) {
                $modx->removeExtensionPackage('articles');
            }
            break;
    }
}
return true;