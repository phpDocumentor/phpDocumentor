<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core;

/**
 * Struct to hold severity levels when logging.
 */
class Log
{
    /** @var string Emergency: system is unstable */
    const EMERG = 0;

    /** @var string Alert: action must be taken immediately */
    const ALERT = 1;

    /** @var string Critical: critical conditions */
    const CRIT = 2;

    /** @var string Error: error conditions */
    const ERR = 3;

    /** @var string Warning: warning conditions */
    const WARN = 4;

    /** @var string Notice: normal but significant condition */
    const NOTICE = 5;

    /** @var string Informational: informational messages */
    const INFO = 6;

    /** @var string Debug: debug messages */
    const DEBUG = 7;

    /** @var string Quiet: disables logging */
    const QUIET = -1;
}