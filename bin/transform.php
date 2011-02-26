#!/usr/bin/env php
<?php
// set path to add lib folder, load the Zend Autoloader and include the symfony timer and graphviz lib
set_include_path(dirname(__FILE__).'/../'.PATH_SEPARATOR.dirname(__FILE__).'/../lib'.PATH_SEPARATOR.get_include_path());
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
  $opts = new Zend_Console_Getopt(DocBlox_Arguments::$transform_options);

  // parse the command line arguments
  $opts->parse();

  // the user has indicated that he would like help
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('');
  }

  // initialize timer
  $timer = new sfTimer();

  $transformer = new DocBlox_Transformer();

  // set target option if it was provided by the user
  $transformer->setTarget($opts->getOption('target')
    ? $opts->getOption('target')
    : 'output'
  );

  $transformer->setSource($opts->getOption('source')
    ? $opts->getOption('source')
    : 'output/structure.xml'
  );

  $transformer->setTemplate($opts->getOption('template')
    ? $opts->getOption('template')
    : 'default'
  );

  // set theme / chrome path if provided
// TODO: should become parameter of the XSLT writer / transformation rule
//  if ($opts->getOption('search'))
//  {
//    if (method_exists($writer, 'setSearchObject'))
//    {
//      $writer->setSearchObject($opts->getOption('search'));
//    }
//    else
//    {
//      echo 'The chosen output format does not support different search methods'.PHP_EOL;
//    }
//  }

  // enable verbose mode if the flag was set
  if ($opts->getOption('verbose'))
  {
    $transformer->setLogLevel(Zend_Log::DEBUG);
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
$transformer->execute();

echo 'Finished transformation in '.round($timer->getElapsedTime(), 2).' seconds'.PHP_EOL;