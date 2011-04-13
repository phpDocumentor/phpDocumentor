#!/usr/bin/env php
<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    CLI
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../src'
  : '@php_dir@/DocBlox/src';

// set path to add lib folder, load the Zend Autoloader and include the symfony timer
set_include_path($base_include_folder . PATH_SEPARATOR . get_include_path());

// bootstrap
require_once('Zend/Loader/Autoloader.php');
require_once('pear/GraphViz.php');
require_once('markdown/markdown.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

$runner = new DocBlox_Task_Runner(($argc == 1) ? 'project:run' : $argv[1]);
$task = $runner->getTask();
try
{
  $task->execute();
}
catch(Exception $e)
{
  echo 'ERROR: '.$e->getMessage().PHP_EOL.PHP_EOL;
  echo $task->getUsageMessage();
}

// disable E_STRICT reporting on the end to prevent PEAR from throwing Strict warnings.
error_reporting(error_reporting() & ~E_STRICT);