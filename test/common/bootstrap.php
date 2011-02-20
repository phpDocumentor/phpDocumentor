<?php

set_include_path(
  get_include_path()
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../..')
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../lib')
);

require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');
