#!/usr/bin/env php
<?php
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

echo 'DocBlox parser version '.DocBlox_Abstract::VERSION.PHP_EOL;

try
{
  $opts = new DocBlox_Arguments();
  $opts->parse();
  $path = $opts->getTarget();
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('Help request received');
  }
} catch (Zend_Console_Getopt_Exception $e)
{
  echo $opts->getUsageMessage();
  exit;
}

$docblox = new DocBlox_Parser();
$docblox->setLogLevel($opts->getOption('verbose') ? Zend_Log::DEBUG : Zend_Log::WARN);
$docblox->setExistingXml(is_readable($path.'/structure.xml') ? file_get_contents($path.'/structure.xml') : null);
$docblox->setIgnorePatterns($opts->getIgnorePatterns());
$docblox->setForced($opts->getOption('force'));
$docblox->setMarkers($opts->getMarkers());

file_put_contents($path.'/structure.xml', $docblox->parseFiles($opts->getFiles()));