<?php

use MODX\Revolution\modSnippet;

/**
 * @var modX $modx
 * @var array $sources
 * @package articles
 * @subpackage build
 */
$snippets = [];

$snippets[1]= $modx->newObject(modSnippet::class);
$snippets[1]->fromArray([
    'id' => 1,
    'name' => 'ArticlesStringSplitter',
    'description' => 'Utility snippet for Articles; splits strings by a delimiter and chunkifys the result.',
    'snippet' => file_get_contents($sources['snippets'].'snippet.articlesstringsplitter.php'),
]);

$snippets[2]= $modx->newObject(modSnippet::class);
$snippets[2]->fromArray([
    'id' => 2,
    'name' => 'Articles',
    'description' => 'Displays Articles for a Container anywhere on your MODX site.',
    'snippet' => file_get_contents($sources['snippets'].'snippet.articles.php'),
]);
return $snippets;