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
    'source|s-s' => 'path where the structure.xml is located (optional, defaults to "output/structure.xml")',
    'target|t-s' => 'path where to save the generated files (optional, defaults to "output")',
    'output|o-s' => 'output format to use (optional, defaults to "xslt")',
    'theme-s'    => 'name of the theme to use (optional, defaults to "default")',
    'search-s'   => 'type of searchbox to use, may be "None", "XmlJs" or "Ajax"',
    'verbose|v'  => 'Outputs any information collected by this application, may slow down the process slightly',
  ));

  // parse the command line arguments
  $opts->parse();

  // the user has indicated that he would like help
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('');
  }

  // initialize timer
  $timer = new sfTimer();

  if ($opts->getOption('output'))
  {
    $writer = $opts->getOption('output');
  } else
  {
    if (!isset(DocBlox_Abstract::config()->transformation->writer))
    {
      throw new Exception('Unable to find configuration entry for the transformation writer, please check your configuration file.');
    }
    $writer = DocBlox_Abstract::config()->transformation->writer;
  }
  $writer = 'DocBlox_Writer_'.ucfirst($writer);
  $writer = new $writer();

  // set target option if it was provided by the user
  if ($opts->getOption('target'))
  {
    $path = realpath($opts->getOption('target'));
    if (!file_exists($path) && !is_dir($path) && !is_writable($path))
    {
      throw new Exception('Given target directory does not exist or is not writable');
    }

    $writer->setTarget($path);
  }

  // set source option if it was provided by the user
  if ($opts->getOption('source'))
  {
    $path = realpath($opts->getOption('source'));
    if (!file_exists($path) || !is_readable($path))
    {
      throw new Exception('Given source does not exist or is not readable');
    }

    $writer->setSource($path);
  }

  // set theme / chrome path if provided
  if ($opts->getOption('theme'))
  {
    $writer->setTheme($opts->getOption('theme'));
  }

  // set theme / chrome path if provided
  if ($opts->getOption('search'))
  {
    if (method_exists($writer, 'setSearchObject'))
    {
      $writer->setSearchObject($opts->getOption('search'));
    }
    else
    {
      echo 'The chosen output format does not support different search methods'.PHP_EOL;
    }
  }

  // enable verbose mode if the flag was set
  if ($opts->getOption('verbose'))
  {
    $writer->setLogLevel(Zend_Log::DEBUG);
  }
} catch (Exception $e)
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

// start the transformation process
echo 'Starting transformation of files (this could take a while depending upon the size of your project)'.PHP_EOL;
$writer->execute();

echo 'Finished transformation in '.round($timer->getElapsedTime(), 2).' seconds'.PHP_EOL;