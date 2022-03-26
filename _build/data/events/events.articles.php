<?php

use MODX\Revolution\modPluginEvent;

/**
 * Adds events to Articles plugin
 *
 * @var modX $modx
 * @package articles
 * @subpackage build
 */
$events = [];

$events['OnPageNotFound']= $modx->newObject(modPluginEvent::class);
$events['OnPageNotFound']->fromArray([
    'event' => 'OnPageNotFound',
    'priority' => 0,
    'propertyset' => 0,
],'',true,true);

$events['OnManagerPageInit']= $modx->newObject(modPluginEvent::class);
$events['OnManagerPageInit']->fromArray([
    'event' => 'OnManagerPageInit',
    'priority' => 0,
    'propertyset' => 0,
],'',true,true);

$events['OnDocPublished']= $modx->newObject(modPluginEvent::class);
$events['OnDocPublished']->fromArray([
    'event' => 'OnDocPublished',
    'priority' => 0,
    'propertyset' => 0,
],'',true,true);

$events['OnDocUnPublished']= $modx->newObject(modPluginEvent::class);
$events['OnDocUnPublished']->fromArray([
    'event' => 'OnDocUnPublished',
    'priority' => 0,
    'propertyset' => 0,
],'',true,true);

return $events;