<?php

use MODX\Revolution\modChunk;

/**
 * @var modX $modx
 * @var array $sources
 * @package articles
 * @subpackage build
 */
$chunks = [];

$chunks[1]= $modx->newObject(modChunk::class);
$chunks[1]->fromArray([
    'id' => 1,
    'name' => 'sample.ArticlesLatestPostTpl',
    'description' => 'The tpl row for the latest post. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'articleslatestpost.chunk.tpl'),
]);

$chunks[2]= $modx->newObject(modChunk::class);
$chunks[2]->fromArray([
    'id' => 2,
    'name' => 'sample.ArticleRowTpl',
    'description' => 'The tpl row for each post when listed on the main Articles Container page. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'articlerow.chunk.tpl'),
]);

$chunks[3]= $modx->newObject(modChunk::class);
$chunks[3]->fromArray([
    'id' => 3,
    'name' => 'sample.ArticlesRss',
    'description' => 'The tpl for the RSS feed. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'articlesrss.chunk.tpl'),
]);

$chunks[4]= $modx->newObject(modChunk::class);
$chunks[4]->fromArray([
    'id' => 4,
    'name' => 'sample.ArticlesRssItem',
    'description' => 'The tpl row for each RSS feed item. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'articlesrssitem.chunk.tpl'),
]);

$chunks[5]= $modx->newObject(modChunk::class);
$chunks[5]->fromArray([
    'id' => 5,
    'name' => 'sample.ArchiveGroupByYear',
    'description' => 'The tpl wrapper for archives when grouped by year.',
    'snippet' => file_get_contents($sources['chunks'].'archivegroupbyyear.chunk.tpl'),
]);

$chunks[6]= $modx->newObject(modChunk::class);
$chunks[6]->fromArray([
    'id' => 6,
    'name' => 'sample.ArticlesRssCategoryNode',
    'description' => 'The tpl for each RSS category node for tagging.',
    'snippet' => file_get_contents($sources['chunks'].'articlesrsscategorynode.chunk.tpl'),
]);
return $chunks;