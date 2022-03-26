<?php

/**
 * Bootstrap file for MODX 3.x
 *
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */
$modx->addPackage('Articles\Model', $namespace['path'] . 'src/', null, 'Articles\\');

$modx->services->add('articles', function() use ($modx) {
    return new Articles\Articles($modx);
});