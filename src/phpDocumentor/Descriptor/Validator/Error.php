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
    /**
     * @var string $severity
     */
    protected $severity;

    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var int $line
     */
    protected $line = 0;

    /**
     * @var array $context
     */
    protected $context = array();

    /**
     * @param string $severity
     * @param string $code
     * @param int $line
     * @param array $context
     */
    public function __construct($severity, $code, $line, array $context = array())
    {
        $this->severity = $severity;
        $this->code     = $code;
        $this->line     = $line;
        $this->context  = $context;
    }

    /**
     * @return string $code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return int $line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string $severity
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @return array $context
     */
    public function getContext()
    {
        return $this->context;
    }
}
