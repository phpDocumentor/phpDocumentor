#!/usr/bin/env php
<?php
// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
$base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
  ? dirname(__FILE__) . '/../'
  : '@php_dir@/DocBlox/';

// set path to add lib folder, load the Zend Autoloader and include the symfony timer
set_include_path($base_include_folder . PATH_SEPARATOR . $base_include_folder . 'lib' . PATH_SEPARATOR . get_include_path());

require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

// gather and parse arguments
try
{
  $opts = new Zend_Console_Getopt(array_merge(DocBlox_Arguments::$transform_options, DocBlox_Arguments::$parse_options));
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

$args = $opts->getRemainingArgs();

if (isset($args[0]) && $args[0] == 'parse')
{
    $callable = $base_include_folder.'bin/parse.php';
}
elseif (isset($args[0]) && $args[0] == 'transform')
{
    $callable = $base_include_folder.'bin/transform.php';
}
else
{
    echo "You can only specify parse or transform actions\n";die();
}

$options = $opts->getOptions();
$optionstring = '';
foreach($options as $option)
{
    $optionstring .= '-'.$option.' '.$opts->getOption($option).' ';
}

passthru('php ' . $callable . ' ' . $optionstring);