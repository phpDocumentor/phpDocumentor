<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once __DIR__ . '/../../src/phpDocumentor/Bootstrap.php';
\phpDocumentor\Bootstrap::createInstance()->initialize();