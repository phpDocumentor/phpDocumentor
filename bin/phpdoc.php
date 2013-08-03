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
$profile = (bool)(getenv('PHPDOC_PROFILE') === 'on');
if ($profile && extension_loaded('xhprof')) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}

// determine base include folder, if @php_dir@ contains @php_dir then
// we did not install via PEAR
$bootstrap_folder = (strpos('@php_dir@', '@php_dir') === 0)
    ? __DIR__ . '/../src'
    : '@php_dir@/phpDocumentor/src';

require_once $bootstrap_folder . '/phpDocumentor/Application.php';
$app = new phpDocumentor\Application();
$app->run();

if (true === $profile) {
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
