#!/usr/bin/env php
<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    CLI
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

// if the file exists then this is not a PEAR install; otherwise pick it up
// from the path
if (file_exists(dirname(__FILE__) . '/docblox.php')) {
    passthru('php '.dirname(__FILE__) . '/docblox.php project:transform ' . implode(' ', array_slice($argv, 1)));
} else {
    passthru('docblox project:transform ' . implode(' ', array_slice($argv, 1)));
}