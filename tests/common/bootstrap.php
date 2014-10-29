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

// @codingStandardsIgnoreFile
require_once __DIR__ . '/../../src/phpDocumentor/Bootstrap.php';

if (!defined('phpDocumentor\PHPUnit\TEMP_DIR')) {
    define('phpDocumentor\PHPUnit\TEMP_DIR', realpath(sys_get_temp_dir()));
}

\phpDocumentor\Bootstrap::createInstance()->initialize();
