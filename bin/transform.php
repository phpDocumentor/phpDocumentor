#!/usr/bin/env php
<?php
// set path to add lib folder, load the Zend Autoloader and include the symfony timer and graphviz lib
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
require_once('pear/GraphViz.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

// output greeting text
echo 'DocBlox transformer version ' . DocBlox_Abstract::VERSION . PHP_EOL . PHP_EOL;

try
{
  // initialize the argument parser
  $opts = new Zend_Console_Getopt(array(
    'help|h'     => 'show this help message',
    'target|t-s' => 'path where the structure.xml is located and where to save the generated files (optional, defaults to "output")',
    'theme-s'      => 'name of the theme to use (optional, defaults to "default")',
    'verbose|v'  => 'Outputs any information collected by this application, may slow down the process slightly',
  ));

  // parse the command line arguments
  $opts->parse();

  // the user has indicated that he would like help
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('');
  }
} catch (Zend_Console_Getopt_Exception $e)
{
  // if the message actually contains anything, show it.
  if ($e->getMessage())
  {
    echo $e->getMessage() . PHP_EOL . PHP_EOL;
  }

  // show help message and exit the application
  echo $opts->getUsageMessage();
  exit;
}

// initialize timer
$timer = new sfTimer();

echo 'Starting transformation of files (this could take a while depending upon the size of your project)'.PHP_EOL;

// TODO: for now we have hardcoded the Xslt writer, this must become a config entry or argument
$writer = new DocBlox_Writer_Xslt();

// set target option if it was provided by the user
if ($opts->getOption('t'))
{
  $path = realpath($opts->getOption('t'));
  if (!file_exists($path) || !is_readable($path.'/structure.xml'))
  {
    throw new Exception('Given target or structure.xml does not exist or is not readable');
  }

  $writer->setTarget($path);
}

// set theme / chrome path if provided
if ($opts->getOption('theme'))
{
  $writer->setTheme($opts->getOption('theme'));
}

// enable verbose mode if the flag was set
if ($opts->getOption('verbose'))
{
  $writer->setLogLevel(Zend_Log::DEBUG);
}

// start the transformation process
$writer->execute();

echo 'Finished transformation in '.round($timer->getElapsedTime(), 2).' seconds'.PHP_EOL;