#!/usr/bin/env php
<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    CLI
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

passthru('php '.dirname(__FILE__).'/docblox.php project:parse '.implode(' ', array_slice($argv, 1)));