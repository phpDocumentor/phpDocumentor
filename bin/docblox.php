#!/usr/bin/env php
<?php
/**
 * DocBlox
 *
 * @category  DocBlox
 * @package   CLI
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

// check whether xhprof is loaded
$profile = false;
if (extension_loaded('xhprof')) {

    // check whether one of the arguments is --profile; this will enable the profiler
    $profile = array_search('--profile', $argv);
    if (false !== $profile) {
        unset($_SERVER['argv'][$profile]);
        $_SERVER['argc']--;
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    }
}

// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../src'
  : '@php_dir@/DocBlox/src';

// set path to add lib folder, load the Zend Autoloader and include the symfony timer
set_include_path($base_include_folder . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

$application = new DocBlox_Core_Application();
$application->main();

if (false !== $profile) {
    include_once 'XHProf/utils/xhprof_lib.php';
    include_once 'XHProf/utils/xhprof_runs.php';

    $xhprof_data = xhprof_disable();
    if ($xhprof_data !== null) {
        $xhprof_runs = new XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, 'docblox');
        $profiler_url = sprintf('index.php?run=%s&source=%s', $run_id, 'docblox');
        echo 'Profile can be found at: ' . $profiler_url . PHP_EOL;
    }
}

// disable E_STRICT reporting on the end to prevent PEAR from throwing Strict warnings.
error_reporting(error_reporting() & ~E_STRICT);
