<?php
/**
 * Articles
 *
 * Copyright 2011-12 by Shaun McCormick <shaun+articles@modx.com>
 *
 * Articles is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Articles is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Articles; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package articles
 */

use MODX\Revolution\modX;

/**
 * Build Schema script
 *
 * @package articles
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package name */
define('PKG_NAME','Articles');
define('PKG_NAME_LOWER',strtolower(PKG_NAME));

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = [
    'root' => $root,
    'core' => $root.'core/components/'.PKG_NAME_LOWER.'/',
    'model' => $root.'core/components/'.PKG_NAME_LOWER.'/src/Model/',
    'assets' => $root.'assets/components/'.PKG_NAME_LOWER.'/',
];

/* load modx and configs */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'vendor/autoload.php';

$modx= new modX();
$modx->initialize('mgr');

echo '<pre>';
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$manager= $modx->getManager();
$generator= $manager->getGenerator();

$generator->classTemplate= <<<EOD
<?php
/**
 * [+phpdoc-package+]
 */
class [+class+] extends [+extends+] {}
?>
EOD;
$generator->platformTemplate= <<<EOD
<?php
/**
 * [+phpdoc-package+]
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\\\', '/') . '/[+class-lowercase+].class.php');
class [+class+]_[+platform+] extends [+class+] {}
?>
EOD;
$generator->mapHeader= <<<EOD
<?php
/**
 * [+phpdoc-package+]
 */
EOD;


/* WARNING
 * parseSchema trashes the model files in the mysql directory. So, don't use for now - edit them manually.
 */

//$generator->parseSchema(
//    $sources['core'] . 'schema/' . PKG_NAME_LOWER . '.mysql.schema.xml',
//    $sources['core'] . '/src/',
//    [
//        'compile'         => null,
//        'update'          => 0,
//        'regenerate'      => 1,
//        'namespacePrefix' => 'Articles\\'
//    ]
//);

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\nExecution time: {$totalTime}\n";

exit ();