<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tests
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

// add the project and its lib path to the include path
set_include_path(
  get_include_path()
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../..')
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../src/lib')
);

// include and initialize the autoloader
require_once('Zend/Loader/Autoloader.php');
require_once('markdown/markdown.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');
