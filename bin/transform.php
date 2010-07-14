#!/usr/bin/env php
<?php
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
require_once('pear/GraphViz.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

$timer = new sfTimer();
echo 'Starting transformation of files (this could take a while depending upon the size of your project)'.PHP_EOL;
$writer = new DocBlox_Writer_Xslt();
$writer->execute();
echo 'Finished transformation in '.$timer->getElapsedTime().'seconds'.PHP_EOL;