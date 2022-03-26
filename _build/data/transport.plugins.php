<?php

use MODX\Revolution\modPlugin;

/**
 * Package in plugins
 *
 * @var modX $modx
 * @var array $sources
 * 
 * @package articles
 * @subpackage build
 */
$plugins = [];

/* create the plugin object */
$plugins[0] = $modx->newObject(modPlugin::class);
$plugins[0]->set('id',1);
$plugins[0]->set('name','ArticlesPlugin');
$plugins[0]->set('description','Handles FURLs for Articles.');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'articles.plugin.php'));
$plugins[0]->set('category', 0);

$events = include $sources['events'].'events.articles.php';
if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for ArticlesPlugin.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for ArticlesPlugin!');
}
unset($events);

return $plugins;