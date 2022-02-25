<?php

use MODX\Revolution\modTemplateVar;

/**
 * @var modX $modx
 * @var array $sources
 * @package articles
 * @subpackage build
 */
$tvs = [];

$tvs[1]= $modx->newObject(modTemplateVar::class);
$tvs[1]->fromArray([
    'id' => 1,
    'name' => 'articlestags',
    'description' => 'The default tags TV for Articles. Do not delete!',
    'caption' => 'articlestags',
    'type' => 'hidden',
]);

return $tvs;