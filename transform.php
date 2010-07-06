<?php
set_include_path(get_include_path().PATH_SEPARATOR.'./lib');
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
require_once('pear/GraphViz.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Nabu_');

$writer = new Nabu_Writer_Xslt();
$writer->execute();