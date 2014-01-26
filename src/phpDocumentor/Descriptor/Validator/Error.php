<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Validator;

/**
 * Struct to record a validation error.
 */
class Error
{
    protected $severity;
    protected $code;
    protected $line = 0;
    protected $context = array();

    public function __construct($severity, $code, $line, array $context = array())
    {
        $this->severity = $severity;
        $this->code     = $code;
        $this->line     = $line;
        $this->context  = $context;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

    public function getContext()
    {
        return $this->context;
    }
}
