#!/usr/bin/env php
<?php
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

try
{
  $opts = new DocBlox_Arguments();
  $opts->parse();
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('Help request received');
  }
} catch (Zend_Console_Getopt_Exception $e)
{
  echo $e->getMessage();
  echo $opts->getUsageMessage();
}

$docblox = new DocBlox_Parser();
$path = $opts->getTarget();
if ($opts->getOption('verbose'))
{
  $docblox->setLogLevel(Zend_Log::DEBUG);
}
$docblox->setIgnorePatterns($opts->getIgnorePatterns());

if (file_exists($path.'/structure.xml'))
{
  $docblox->setExistingXml(file_get_contents($path.'/structure.xml'));
}
$docblox->setForced($opts->getOption('force'));
file_put_contents($path.'/structure.xml', $docblox->parseFiles($opts->getFiles()));