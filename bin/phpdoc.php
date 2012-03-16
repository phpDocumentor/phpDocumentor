#!/usr/bin/env php
<?php
/**
 * phpDocumentor
 *
 * @category  phpDocumentor
 * @package   CLI
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
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

// determine base include folder, if @php_dir@ contains @php_dir then
// we did not install via PEAR
$bootstrap_folder = (strpos('@php_dir@', '@php_dir') === 0)
    ? dirname(__FILE__) . '/../src'
    : '@php_dir@/phpDocumentor/src';

require($bootstrap_folder . '/phpDocumentor/Bootstrap.php');

$autoloader = phpDocumentor_Bootstrap::createInstance()->registerAutoloader();

$task_name = ($_SERVER['argc'] == 1) ? false : $_SERVER['argv'][1];
$runner    = new phpDocumentor_Task_Runner($task_name, 'project:run');
$task      = $runner->getTask();

if (!$task->getQuiet() && (!$task->getProgressbar())) {
    phpDocumentor_Core_Abstract::renderVersion();
} else {
    phpDocumentor_Core_Abstract::config()->logging->level = 'quiet';
}
if ($task->getVerbose()) {
    phpDocumentor_Core_Abstract::config()->logging->level = 'debug';
}

// the plugins are registered here because the phpDocumentor_Task can load a
// custom configuration; which is needed by this registration
phpDocumentor_Bootstrap::createInstance()->registerPlugins($autoloader);

try {
    $task->execute();
} catch (Exception $e) {
    if (!$task->getQuiet()) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
        echo $task->getUsageMessage();
    }
    die(1);
}

if (false !== $profile) {
    include_once 'XHProf/utils/xhprof_lib.php';
    include_once 'XHProf/utils/xhprof_runs.php';

    $xhprof_data = xhprof_disable();
    if ($xhprof_data !== null) {
        $xhprof_runs = new XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, 'phpdoc');
        $profiler_url = sprintf('index.php?run=%s&source=%s', $run_id, 'phpdoc');
        echo 'Profile can be found at: ' . $profiler_url . PHP_EOL;
    }
}

// disable E_STRICT reporting on the end to prevent PEAR from throwing Strict warnings.
error_reporting(error_reporting() & ~E_STRICT);