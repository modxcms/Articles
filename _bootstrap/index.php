<?php

/**
 * Use this to quickly bootstrap the git version of Articles into MODX 3.x for development and testing.
 *
 * This is a modified version of the bootstrap script written by Mark Hamstra ( modmore.com ) for Commerce_ModuleSkeleton
 * https://github.com/modmore/Commerce_ModuleSkeleton/blob/master/_bootstrap/index.php
 *
 * plus it incorporates sections of the current Articles build script and dependencies.
 */

use MODX\Revolution\modX;
use MODX\Revolution\modCategory;
use MODX\Revolution\modChunk;
use MODX\Revolution\modNamespace;
use MODX\Revolution\modPlugin;
use MODX\Revolution\modPluginEvent;
use MODX\Revolution\modSnippet;
use MODX\Revolution\modSystemSetting;
use MODX\Revolution\modTemplate;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarTemplate;
use MODX\Revolution\Transport\modTransportPackage;
use MODX\Revolution\Transport\modTransportProvider;

use Articles\Articles;

require_once __DIR__ . '/../_build/includes/functions.php';

/* Define sources used in build script, so we can use scripts in /_build/data/ */
define('PKG_NAME','Articles');
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
$root = dirname(dirname(__FILE__)).'/';
$sources = [
    'root' => $root,
    'build' => $root .'_build/',
    'resolvers' => $root . '_build/resolvers/',
    'subpackages' => $root . '_build/subpackages/',
    'data' => $root . '_build/data/',
    'events' => $root . '_build/data/events/',
    'permissions' => $root . '_build/data/permissions/',
    'properties' => $root . '_build/data/properties/',
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'plugins' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
    'snippets' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/snippets/',
    'chunks' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/chunks/',
    'templates' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/templates/',
    'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'model' => $root.'core/components/'.PKG_NAME_LOWER.'/Model/',
];
unset($root);


/* Get the core config */
if (!file_exists($sources['root'] . '/config.core.php')) {
    die('ERROR: missing '. $sources['root'] . '/config.core.php file defining the MODX core path.');
}

echo "<pre>";
echo "Loading MODX...\n";
require_once $sources['root'] . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
echo "Initializing manager...\n";
$modx->initialize('mgr');
//$modx->getService('error','error.modError', '', '');
$modx->setLogTarget('HTML');

$articles = $modx->services->get('articles');
if (!($articles instanceof Articles)) die(0);

/* Namespace */
if (!createObject(modNamespace::class, [
    'name' => 'articles',
    'path' => $sources['root'].'/core/components/articles/',
    'assets_path' => $sources['root'].'/assets/components/articles/',
],'name', false)) {
    echo "Error creating namespace articles.\n";
}

/* Path settings */
if (!createObject(modSystemSetting::class, [
    'key' => 'articles.core_path',
    'value' => $sources['root'].'/core/components/articles/',
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'Paths',
    'editedon' => time(),
], 'key', false)) {
    echo "Error creating articles.core_path setting.\n";
}

if (!createObject(modSystemSetting::class, [
    'key' => 'articles.assets_path',
    'value' => $sources['root'].'/assets/components/articles/',
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'Paths',
    'editedon' => time(),
], 'key', false)) {
    echo "Error creating articles.assets_path setting.\n";
}

/* Fetch assets url */
$requestUri = $_SERVER['REQUEST_URI'] ?: __DIR__ . '/_bootstrap/index.php';
$bootstrapPos = strpos($requestUri, '_bootstrap/');
$requestUri = rtrim(substr($requestUri, 0, $bootstrapPos), '/').'/';
$assetsUrl = "{$requestUri}assets/components/articles/";

if (!createObject(modSystemSetting::class, [
    'key' => 'articles.assets_url',
    'value' => $assetsUrl,
    'xtype' => 'textfield',
    'namespace' => 'articles',
    'area' => 'Paths',
    'editedon' => time(),
], 'key', false)) {
    echo "Error creating articles.assets_url setting.\n";
}

/* Other settings */
$settings = include dirname(__DIR__) . '/_build/data/transport.settings.php';
foreach ($settings as $key => $opts) {
    $opts = $opts->toArray();
    $val = $opts['value'];

    if (isset($opts['xtype'])) $xtype = $opts['xtype'];
    elseif (is_int($val)) $xtype = 'numberfield';
    elseif (is_bool($val)) $xtype = 'modx-combo-boolean';
    else $xtype = 'textfield';

    if (!createObject(modSystemSetting::class, [
        'key' => 'articles.' . $key,
        'value' => $opts['value'],
        'xtype' => $xtype,
        'namespace' => 'articles',
        'area' => $opts['area'],
        'editedon' => time(),
    ], 'key', false)) {
        echo "Error creating articles.".$key." setting.\n";
    }
}

if (!createObject(modCategory::class, [
    'category' => PKG_NAME,
    'parent' => 0,
    'rank' => 0
], ['category'], true)) {
    echo "Error creating Articles category.\n";
}
$category = $modx->getObject(modCategory::class, ['category' => PKG_NAME]);
if (!$category) {
    echo "Error creating category!\n";
}

/* Plugins */
$plugins = include dirname(__DIR__) . '/_build/data/transport.plugins.php';
foreach ($plugins as $key => $pluginOpts) {
    $pluginOpts = $pluginOpts->toArray();

    if (!createObject(modPlugin::class, [
        'name' => $pluginOpts['name'],
        'description' => $pluginOpts['description'],
        'category' => $category->get('id'),
        'static' => true,
        'static_file' => $sources['plugins'] . 'articles.plugin.php',
    ], 'name', true)) {
        echo "Error creating Articles plugin.\n";
    }

    /* Plugin events */
    $plugin = $modx->getObject(modPlugin::class, ['name' => $pluginOpts['name']]);
    $pluginEvents = include dirname(__DIR__) . '/_build/data/events/events.articles.php';
    foreach ($pluginEvents as $eventKey => $eventOpts) {
        $eventOpts = $eventOpts->toArray();

        if (!createObject(modPluginEvent::class, [
            'pluginid' => $plugin->get('id'),
            'event' => $eventOpts['event'],
            'priority' => 0,
        ], ['pluginid','event'], false)) {
            echo "Error creating modPluginEvent.\n";
        }
    }
}

/* Snippets */
$snippets = include dirname(__DIR__) . '/_build/data/transport.snippets.php';
foreach ($snippets as $key => $opts) {
    $opts = $opts->toArray();

    if (!createObject(modSnippet::class, [
        'name' => $opts['name'],
        'description' => $opts['description'],
        'category' => $category->get('id'),
        'static' => true,
        'static_file' => $sources['snippets'] . 'snippet.' . strtolower($opts['name']) . '.php',
    ], 'name', true)) {
        echo "Error creating {$opts['name']} snippet.\n";
    }
}

/* Chunks */
$chunks = include dirname(__DIR__) . '/_build/data/transport.chunks.php';
foreach ($chunks as $key => $opts) {
    $opts = $opts->toArray();

    if (!createObject(modChunk::class, [
        'name' => $opts['name'],
        'description' => $opts['description'],
        'category' => $category->get('id'),
        'static' => true,
        'static_file' => $sources['chunks'] . strtolower($opts['name']) . '.chunk.tpl',
    ], 'name', true)) {
        echo "Error creating {$opts['name']} chunk.\n";
    }
}

/* Templates */
$templates = include dirname(__DIR__) . '/_build/data/transport.templates.php';
foreach ($templates as $key => $opts) {
    $opts = $opts->toArray();

    if (!createObject(modTemplate::class, [
        'templatename' => $opts['templatename'],
        'description' => $opts['description'],
        'category' => $category->get('id'),
        'static' => true,
        'static_file' => $sources['templates'] . strtolower(str_replace('sample.', '', $opts['templatename'])) . '.tpl',
    ], ['templatename'], true)) {
        echo "Error creating {$opts['templatename']} template.\n";
    }
}

/* TVs */
$tvs = include dirname(__DIR__) . '/_build/data/transport.tvs.php';
foreach ($tvs as $key => $opts) {
    $opts = $opts->toArray();

    if (!createObject(modTemplateVar::class, [
        'name' => $opts['name'],
        'description' => $opts['description'],
        'category' => $category->get('id'),
        'caption' => $opts['caption'],
        'type' => $opts['type']
    ], 'name', true)) {
        echo "Error creating {$opts['name']} TV.\n";
    }

    $tv = $modx->getObject(modTemplateVar::class, [
        'name' => $opts['name'],
    ]);
    if ($tv) {
        $templates = ['sample.ArticlesContainerTemplate', 'sample.ArticleTemplate'];
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
                    $templateVarTemplate->set('templateid', $template->get('id'));
                    $templateVarTemplate->set('tmplvarid', $tv->get('id'));
                    $templateVarTemplate->save();
                }
            }
        }
    }
}

$modx->addExtensionPackage('articles', $sources['model']);

/* Dependencies */
/**
 * Define required packages: name => minimum version
 */
$packages = [
    'archivist' => '1.2.4',
    'getpage' => '1.2.3',
    'getresources' => '1.7.0',
    'quip' => '2.3.4',
    'taglister' => '1.1.7',
];

/** @var modTransportProvider $provider */
$provider = $modx->getObject('transport.modTransportProvider', [
    'service_url' => 'https://rest.modx.com/extras/',
]);
if (!$provider) {
    $modx->log(modX::LOG_LEVEL_ERROR, "Could not find MODX.com provider; can't install dependencies");
}

foreach ($packages as $package_name => $version) {
    $modx->log(modX::LOG_LEVEL_INFO, "Installing dependency <b>{$package_name}</b> v{$version} (or higher)...");

    $installed = $modx->getIterator('transport.modTransportPackage', [
        'package_name' => $package_name,
    ]);
    /** @var modTransportPackage $package */
    foreach ($installed as $package) {
        if ($package->compareVersion($version, '<=')) {
            $modx->log(modX::LOG_LEVEL_INFO, "- &check; {$package->get('signature')} already installed");
            continue(2);
        }
    }


    $latest = $provider->latest($package_name, '>=' . $version);
    if (count($latest) === 0) {
        $modx->log(modX::LOG_LEVEL_ERROR, "- Could not find <b>{$package_name} v{$version}+</b> in package provider {$provider->get('name')}");
        $success = false;
        continue;
    }

    $latest = reset($latest);
    $modx->log(modX::LOG_LEVEL_INFO, "- Downloading <b>{$latest['signature']}</b> from {$provider->get('name')}...");
    $package = $provider->transfer($latest['signature']);

    if (!$package) {
        $modx->log(modX::LOG_LEVEL_ERROR, "- Download failed :(");
        $success = false;
        continue;
    }

    $modx->log(modX::LOG_LEVEL_WARN, "<b>--- Installing {$latest['signature']} ---</b>");
    $stime = microtime(true);
    $installSuccess = $package->install();
    $ttime = microtime(true) - $stime;

    if ($installSuccess) {
        $modx->log(modX::LOG_LEVEL_WARN,"<b>--- Installed {$latest['signature']} in " . number_format($ttime, 2) . "s ---</b>");
    }
    else {
        $modx->log(modX::LOG_LEVEL_ERROR,"- Installation failed. Please refer to the log above for details.");
        $success = false;
    }
}



/**
 * Creates an object.
 *
 * @param string $className
 * @param array $data
 * @param string $primaryField
 * @param bool $update
 * @return bool
 */
function createObject ($className = '', array $data = [], $primaryField = '', $update = true) {
    global $modx;
    /* @var xPDOObject $object */
    $object = null;

    /* Attempt to get the existing object */
    if (!empty($primaryField)) {
        if (is_array($primaryField)) {
            $condition = [];
            foreach ($primaryField as $key) {
                $condition[$key] = $data[$key];
            }
        }
        else {
            $condition = [$primaryField => $data[$primaryField]];
        }
        $object = $modx->getObject($className, $condition);
        if ($object instanceof $className) {
            if ($update) {
                $object->fromArray($data);
                return $object->save();
            } else {
                $condition = $modx->toJSON($condition);
                echo "Skipping {$className} {$condition}: already exists.\n";
                return true;
            }
        }
    }

    /* Create new object if it doesn't exist */
    if (!$object) {
        $object = $modx->newObject($className);
        $object->fromArray($data, '', true);
        return $object->save();
    }

    return false;
}