<?php
// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../../src'
  : '@php_dir@/DocBlox/src';

// set path to add lib folder, load the Zend Autoloader
set_include_path($base_include_folder . PATH_SEPARATOR . get_include_path());

require_once dirname(__FILE__) . '/../../src/DocBlox/Bootstrap.php';

$autoloader = DocBlox_Bootstrap::createInstance()->registerAutoloader();

$task_name = ($_SERVER['argc'] == 1) ? false : $_SERVER['argv'][1];
$runner = new DocBlox_Task_Runner($task_name, 'project:run');
$task = $runner->getTask();

if (!$task->getQuiet() && (!$task->getProgressbar())) {
    DocBlox_Core_Abstract::renderVersion();
} else {
    DocBlox_Core_Abstract::config()->logging->level = 'quiet';
}
if ($task->getVerbose()) {
    DocBlox_Core_Abstract::config()->logging->level = 'debug';
}

// the plugins are registered here because the DocBlox_Task can load a
// custom configuration; which is needed by this registration
DocBlox_Bootstrap::createInstance()->registerPlugins($autoloader);

try {
    $task->execute();
} catch (Exception $e) {
    if (!$task->getQuiet()) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
        echo $task->getUsageMessage();
    }
    die(1);
}
