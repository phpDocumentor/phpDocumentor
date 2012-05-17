#!/usr/bin/env php
<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

// if the file exists then this is not a PEAR install; otherwise pick it up
// from the path
if (file_exists(dirname(__FILE__) . '/phpdoc.php')) {
    passthru(
        'php '.dirname(__FILE__).'/phpdoc.php project:parse '
        .implode(' ', array_slice($argv, 1))
    );
} else {
    passthru('phpdoc project:parse '.implode(' ', array_slice($argv, 1)));
}
