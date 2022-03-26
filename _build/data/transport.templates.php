<?php

use MODX\Revolution\modTemplate;

/**
 * @var modX $modx
 * @var array $sources
 * @package articles
 * @subpackage build
 */
$templates = [];

$templates[1]= $modx->newObject(modTemplate::class);
$templates[1]->fromArray([
    'id' => 1,
    'templatename' => 'sample.ArticlesContainerTemplate',
    'description' => 'The default Template for the Articles Container. Duplicate this to override it.',
    'content' => file_get_contents($sources['templates'].'articlescontainertemplate.tpl'),
]);

$templates[2]= $modx->newObject(modTemplate::class);
$templates[2]->fromArray([
    'id' => 2,
    'templatename' => 'sample.ArticleTemplate',
    'description' => 'The default Template for an Article. Duplicate this to override it.',
    'content' => file_get_contents($sources['templates'].'articletemplate.tpl'),
]);

return $templates;