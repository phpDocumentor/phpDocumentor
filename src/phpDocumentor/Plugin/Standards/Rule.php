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

namespace phpDocumentor\Plugin\Standards;

use Psr\Log\LogLevel;

/**
 * A single instruction to the validation system to verify and report a violation by its name.
 *
 * A Rule may either refer to a named Sniff or to another Ruleset object. When a Rule refers to a Sniff then before
 * phpDocumentor starts parsing a Project the associated Sniff will be enabled. This will in turn register a Validation
 * on a Descriptor class or a property of a Descriptor class.
 *
 * It does this explicitly on the class and not the object so that all Descriptors of that class are verified with the
 * Validator that is part of the Sniff with this Rule.
 *
 * In this Rule we can override the default violation message and severity of the issue. This gives us control on how
 * we want to check a project.
 *
 * In addition to the above a Rule may also exclude a path from these verifications or, when referring to another
 * Ruleset, exclude specific rules from the sub-Ruleset.
 */
final class Rule
{
    const SEVERITY_SILENT    = 0;
    const SEVERITY_DEBUG     = 1;
    const SEVERITY_INFO      = 2;
    const SEVERITY_NOTICE    = 3;
    const SEVERITY_WARNING   = 4;
    const SEVERITY_ERROR     = 5;
    const SEVERITY_CRITICAL  = 6;
    const SEVERITY_ALERT     = 7;
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
     * A series of rule names that are not imported when the ref property refers to another ruleset.
     *
     * @var string[]
     */
    private $exclude = array();

    /**
     * A series of file and folder names (including wildcards) that will not be checked by this rule or the ruleset
     * references by the ref parameter.
     *
     * @todo Not Implemented Yet; have to figure out a clean way to do this.
     *
     * @var string[]
     */
    private $excludePattern = array();

    /**
     * Initializes this rule with a reference, message and optionally a severity or series of properties.
     *
     * @param string|Ruleset $ref        A reference to a Sniff or other Ruleset
     * @param string         $message    The message used to override the Sniffs default.
     * @param integer        $severity   The severity of this specific Rule, this will override the default.
     * @param string[]       $properties Custom properties for a Sniff, some sniffs allow some configuration.
     */
    public function __construct($ref, $message, $severity = self::SEVERITY_ERROR, $properties = array())
    {
        $this->ref        = $ref;
        $this->message    = $message;
        $this->severity   = $severity;
        $this->properties = $properties;
    }

    /**
     * Returns the name of the Sniff to which this Rule applies or another Ruleset to include.
     *
     * @return string|Ruleset
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Returns the message that is to be returned on a Violation.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns an integer indicating the severity of this Rule.
     *
     * @return integer one of the SEVERITY_* constants in this class.
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Returns the severity identifier in this Rule as a LogLevel, as accepted by PSR-3 compatible loggers.
     *
     * @return string|null
     */
    public function getSeverityAsLogLevel()
    {
        switch ($this->severity) {
            case self::SEVERITY_DEBUG:
                return LogLevel::DEBUG;
            case self::SEVERITY_INFO:
                return LogLevel::INFO;
            case self::SEVERITY_NOTICE:
                return LogLevel::NOTICE;
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
     * Returns the properties that are registered with this Rule.
     *
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Registers a series of names of Sniffs/Rules to exclude from an referenced Ruleset.
     *
     * @param string[] $exclude
     *
     * @return void
     */
    public function setExclude(array $exclude)
    {
        if (!$this->getRef() instanceof Ruleset) {
            throw new \RuntimeException('Unable to exclude rules because the reference is not a Ruleset');
        }

        $this->exclude = $exclude;
    }

    /**
     * Returns a series of names of Sniffs/Rules to exclude from an referenced Ruleset.
     *
     * @return string[]
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * Registers a series of files and folders (including wildcards) of paths that should be excluded from this Rule.
     *
     * @param string[] $excludePattern
     *
     * @return void
     */
    public function setExcludePattern($excludePattern)
    {
        $this->excludePattern = $excludePattern;
    }

    /**
     * Returns a series of files and folders (including wildcards) of paths that should be excluded from this Rule.
     *
     * @return string[]
     */
    public function getExcludePattern()
    {
        return $this->excludePattern;
    }
}
