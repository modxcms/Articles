<?php

use MODX\Revolution\modSystemSetting;

/**
 * @var modX $modx
 * @package articles
 * @subpackage build
 */
$settings = [];
$settings['articles.container_ids']= $modx->newObject(modSystemSetting::class);
$settings['articles.container_ids']->fromArray([
    'key' => 'articles.container_ids',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'furls',
],'',true,true);
$settings['articles.default_container_template']= $modx->newObject(modSystemSetting::class);
$settings['articles.default_container_template']->fromArray([
    'key' => 'articles.default_container_template',
    'value' => 0,
    'xtype' => 'modx-combo-template',
    'namespace' => 'articles',
    'area' => 'site',
],'',true,true);
$settings['articles.default_article_template']= $modx->newObject(modSystemSetting::class);
$settings['articles.default_article_template']->fromArray([
    'key' => 'articles.default_article_template',
    'value' => 0,
    'xtype' => 'modx-combo-template',
    'namespace' => 'articles',
    'area' => 'site',
],'',true,true);
$settings['articles.default_article_sort_field']= $modx->newObject(modSystemSetting::class);
$settings['articles.default_article_sort_field']->fromArray([
    'key' => 'articles.default_article_sort_field',
    'value' => 'createdon',
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'site',
],'',true,true);
$settings['articles.article_show_longtitle']= $modx->newObject(modSystemSetting::class);
$settings['articles.article_show_longtitle']->fromArray([
    'key' => 'articles.article_show_longtitle',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'articles',
    'area' => 'site',
],'',true,true);
$settings['articles.mgr_date_format']= $modx->newObject(modSystemSetting::class);
$settings['articles.mgr_date_format']->fromArray([
    'key' => 'articles.mgr_date_format',
    'value' => '%b %d',
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'site',
],'',true,true);
$settings['articles.mgr_time_format']= $modx->newObject(modSystemSetting::class);
$settings['articles.mgr_time_format']->fromArray([
    'key' => 'articles.mgr_time_format',
    'value' => '%H:%I %p',
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'site',
],'',true,true);

return $settings;