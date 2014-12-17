<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

// @codingStandardsIgnoreFile
require_once __DIR__ . '/../../src/phpDocumentor/Bootstrap.php';

if (!defined('phpDocumentor\PHPUnit\TEMP_DIR')) {
    define('phpDocumentor\PHPUnit\TEMP_DIR', realpath(sys_get_temp_dir()));
}

\phpDocumentor\Bootstrap::createInstance()->initialize();
