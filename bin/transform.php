#!/usr/bin/env php
<?php
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
require_once('pear/GraphViz.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

try
{
  $opts = new Zend_Console_Getopt(array(
    'help|h'     => 'show this help message',
    'target|t-s' => 'path where the structure.xml is located and where to save the generated files (optional, defaults to "output")',
    'theme-s'      => 'name of the theme to use (optional, defaults to "default")',
    'verbose|v'  => 'Outputs any information collected by this application, may slow down the process slightly',
  ));
  $opts->parse();
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('Help request received');
  }
} catch (Zend_Console_Getopt_Exception $e)
{
  echo $opts->getUsageMessage();
  exit;
}

$timer = new sfTimer();
echo 'Starting transformation of files (this could take a while depending upon the size of your project)'.PHP_EOL;
$writer = new DocBlox_Writer_Xslt();

if ($opts->getOption('t'))
{
  $path = realpath($opts->getOption('t'));
  if (!file_exists($path) || !file_exists($path.'/structure.xml'))
  {
    throw new Exception('Given target or structure.xml does not exist');
  }
  $writer->setTarget($path);
}
if ($opts->getOption('theme'))
{
  $theme = $opts->getOption('theme');
  $writer->setTheme($theme);
}
if ($opts->getOption('verbose'))
{
  $writer->setLogLevel(Zend_Log::DEBUG);
}

$writer->execute();
echo 'Finished transformation in '.$timer->getElapsedTime().' seconds'.PHP_EOL;