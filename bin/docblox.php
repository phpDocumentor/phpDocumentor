#!/usr/bin/env php
<?php
// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../'
  : '@php_dir@/DocBlox/';

// set path to add lib folder, load the Zend Autoloader and include the symfony timer
set_include_path($base_include_folder . PATH_SEPARATOR . $base_include_folder . 'lib' . PATH_SEPARATOR . get_include_path());

// if no task was given; execute the list task by default
if ($argc == 1)
{
  $argv[] = 'project:list';
}

// bootstrap
require_once('Zend/Loader/Autoloader.php');
require_once('pear/GraphViz.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

// find the task which we want to use
$class_name = 'DocBlox_Task_Project_Parse';
$task_parts = explode(':', $argv[1]);
unset($argv[1]);
if (count($task_parts) == 1)
{
  array_unshift($task_parts, 'project');
}
$class_name = 'DocBlox_Task_'.ucfirst($task_parts[0]).'_'. ucfirst($task_parts[1])  ;

// sorry about the shut up operator but we do this check to determine whether this works
// and Zend_Loader throws a warning if the class does not exist.
if (!@class_exists($class_name))
{
  echo 'DocBlox version ' . DocBlox_Abstract::VERSION . PHP_EOL . PHP_EOL;
  echo 'ERROR: Unable to execute task: '.implode(':', $task_parts).', it is not found'.PHP_EOL.PHP_EOL;
  exit(1);
}

/** @var DocBlox_Task_Abstract $task  */
$task = new $class_name();
try
{
  $task->parse();
  $task->execute();
}
catch(Exception $e)
{
  echo 'ERROR: '.$e->getMessage().PHP_EOL.PHP_EOL;
  echo $task->getUsageMessage();
}