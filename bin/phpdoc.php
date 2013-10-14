#!/usr/bin/env php
<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

// check whether xhprof is loaded
$profile   = (bool)(getenv('PHPDOC_PROFILE') === 'on');
$xhguiPath = getenv('XHGUI_PATH');
if ($profile && $xhguiPath && extension_loaded('xhprof')) {
    echo 'PROFILING ENABLED' . PHP_EOL;
    include($xhguiPath . '/external/header.php');
}

// determine base include folder, if @php_dir@ contains @php_dir then
// we did not install via PEAR
$bootstrap_folder = (strpos('@php_dir@', '@php_dir') === 0)
    ? __DIR__ . '/../src'
    : '@php_dir@/phpDocumentor/src';

require_once $bootstrap_folder . '/phpDocumentor/Application.php';
$app = new phpDocumentor\Application();
$app->run();

// disable E_STRICT reporting on the end to prevent PEAR from throwing Strict warnings.
error_reporting(error_reporting() & ~E_STRICT);
