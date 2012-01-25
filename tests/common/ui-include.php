<?php
// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../../src'
  : '@php_dir@/phpDocumentor/src';

// set path to add lib folder, load the Zend Autoloader
set_include_path($base_include_folder . PATH_SEPARATOR . get_include_path());

require_once dirname(__FILE__) . '/../../src/phpDocumentor/Bootstrap.php';

$autoloader = phpDocumentor_Bootstrap::createInstance()->registerAutoloader();

$task_name = ($_SERVER['argc'] == 1) ? false : $_SERVER['argv'][1];
$runner = new phpDocumentor_Task_Runner($task_name, 'project:run');
$task = $runner->getTask();

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
