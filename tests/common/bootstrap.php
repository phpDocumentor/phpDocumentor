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
  . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../src')
);

require_once('PHPUnit/Extensions/OutputTestCase.php');

// include and initialize the autoloader
require_once dirname(__FILE__) . '/../../src/DocBlox/Bootstrap.php';
DocBlox_Bootstrap::createInstance()->registerAutoloader();