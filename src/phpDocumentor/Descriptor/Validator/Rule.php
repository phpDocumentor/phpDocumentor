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

use Psr\Log\LogLevel;

final class Rule
{
    const SEVERITY_SILENT = 0;
    const SEVERITY_DEBUG = 1;
    const SEVERITY_INFO = 2;
    const SEVERITY_NOTICE = 3;
    const SEVERITY_WARNING = 4;
    const SEVERITY_ERROR = 5;
    const SEVERITY_CRITICAL = 6;
    const SEVERITY_ALERT = 7;
    const SEVERITY_EMERGENCY = 8;

    /**
     * A reference to a specific violation or other ruleset.
     *
     * This property may contain a reference to
     *
     * - the code for a specific violation (a validator could throw several violations; we are interested in
     *   controlling which violations are actually passed).
     * - the name of another pre-defined ruleset
     * - the location of an XML file containing a ruleset
     *
     * @var string|Ruleset
     */
    private $ref = '';

    /**
     * Contains the message that needs to be output when a violation occurs.
     *
     * Note: the contents of this property are only used when {@see self::$ref} refers to a Violation and not a Ruleset.
     *
     * @var string
     */
    private $message = '';

    /**
     * Overrides the default severity for a violation.
     *
     * The severity for phpDocumentor works slightly different than it does for PHP_CodeSniffer; in PHP_CodeSniffer
     * severity is a number that does not directly map to one of the LogLevels but is a relative number to what is
     * provided on the command line.
     *
     * Because we do work with normalized LogLevels phpDocumentor maps the severity number according to the SEVERITY
     * constants in this class.
     *
     * Note: the contents of this property are only used when {@see self::$ref} refers to a Violation and not a Ruleset.
     *
     * @var integer
     */
    private $severity = self::SEVERITY_ERROR;

    /**
     * Some validations/violations are configurable in the way they work, using properties we can influence their
     * behavior.
     *
     * Note: the contents of this property are only used when {@see self::$ref} refers to a Violation and not a Ruleset.
     *
     * @var string[]
     */
    private $properties = array();

    /**
     *
     *
     * @var string[]
     */
    private $exclude = array();

    private $excludePattern = array();

    public function __construct($ref, $message, $severity = 5, $properties = array())
    {
        $this->ref        = $ref;
        $this->message    = $message;
        $this->severity   = $severity;
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    public function getSeverityAsLogLevel()
    {
        switch ($this->severity) {
            case self::SEVERITY_DEBUG:
                return LogLevel::DEBUG;
            case self::SEVERITY_NOTICE:
                return LogLevel::NOTICE;
            case self::SEVERITY_INFO:
                return LogLevel::INFO;
            case self::SEVERITY_WARNING:
                return LogLevel::WARNING;
            case self::SEVERITY_ERROR:
                return LogLevel::ERROR;
            case self::SEVERITY_CRITICAL:
                return LogLevel::CRITICAL;
            case self::SEVERITY_ALERT:
                return LogLevel::ALERT;
            case self::SEVERITY_EMERGENCY:
                return LogLevel::EMERGENCY;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param mixed $exclude
     */
    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
    }

    /**
     * @return mixed
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param mixed $excludePattern
     */
    public function setExcludePattern($excludePattern)
    {
        $this->excludePattern = $excludePattern;
    }

    /**
     * @return mixed
     */
    public function getExcludePattern()
    {
        return $this->excludePattern;
    }
}
