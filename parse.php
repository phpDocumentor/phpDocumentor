<?php
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Nabu_');

require_once('Nabu.php');

try
{
  $opts = new Nabu_Arguments();
  $opts->parse();
  if ($opts->getOption('h'))
  {
    throw new Zend_Console_Getopt_Exception('Help request received');
  }
} catch (Zend_Console_Getopt_Exception $e)
{
  echo $opts->getUsageMessage();
}

$nabu = new Nabu();
$path = $opts->getTarget();
if ($opts->getOption('verbose'))
{
  $nabu->setLogLevel(Zend_Log::DEBUG);
}

file_put_contents($path.'/structure.xml', $nabu->parseFiles($opts->getFiles()));


//$file = $nabu->parseFile('../../Projects/emma.unet.nl/plugins/emmaProductManagementPlugin/lib/model/om/BaseFeature.php');
//file_put_contents('structure.xml', $nabu->parseDirectory('.'));
//file_put_contents('structure.xml', $nabu->parseDirectory('../../Projects/emma.unet.nl/plugins/emmaProductManagementPlugin/lib/model/'));
