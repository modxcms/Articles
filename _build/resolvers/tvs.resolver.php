<?php

use MODX\Revolution\modTemplate;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarTemplate;
use xPDO\Transport\xPDOTransport;

/**
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
            $modelPath = $modx->getOption('articles.core_path',null,$modx->getOption('core_path').'components/articles/').'model/';

            /** @var modTemplateVar $tv */
            $tv = $modx->getObject(modTemplateVar::class, [
                'name' => 'articlestags',
            ]);
            if ($tv) {
                $templates = ['sample.ArticlesContainerTemplate','sample.ArticleTemplate'];
                foreach ($templates as $templateName) {
                    /** @var modTemplate $template */
                    $template = $modx->getObject(modTemplate::class, ['templatename' => $templateName]);
                    if ($template) {
                        /** @var modTemplateVarTemplate $templateVarTemplate */
                        $templateVarTemplate = $modx->getObject(modTemplateVarTemplate::class, [
                            'templateid' => $template->get('id'),
                            'tmplvarid' => $tv->get('id'),
                        ]);
                        if (!$templateVarTemplate) {
                            $templateVarTemplate = $modx->newObject(modTemplateVarTemplate::class);
                            $templateVarTemplate->set('templateid',$template->get('id'));
                            $templateVarTemplate->set('tmplvarid',$tv->get('id'));
                            $templateVarTemplate->save();
                        }
                    }
                }
            }
            break;
    }
}
return true;