<?php
/**
 * phpDocumentor
 *
 * @category   phpDocumentor
 * @package    Tests
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

require_once('PHPUnit/Extensions/OutputTestCase.php');

// include and initialize the autoloader
require_once __DIR__ . '/../../src/phpDocumentor/Bootstrap.php';
phpDocumentor_Bootstrap::createInstance()->initialize();
