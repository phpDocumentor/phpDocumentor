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

/**
 * A collection of Rules that together form a Documentation Standard.
 *
 * A Ruleset is a series of Rules that determine what properties of a Project are 'sniffed', or checked. A Ruleset may
 * also exclude a series of paths from the checking by child Rules (and subsequent Child Rulesets references by those
 * Rules).
 *
 * @todo do not enable Sniffs that are excluded in a Rule
 * @todo apply exclusion rules to child Rules.
 */
class Ruleset
{
    /** @var string */
    private $name;

    /** @var string */
    private $description = '';

    /** @var Rule[] */
    private $rules = array();

    /** @var string[] */
    private $excludePatterns = array();

    /**
     * Initializes this ruleset with its name and a series of initial rules.
     *
     * @param string $name
     * @param Rule[] $rules
     */
    public function __construct($name, array $rules = array())
    {
        $this->name  = $name;
        $this->rules = $rules;
    }

    /**
     * Returns the identifying name for this Ruleset.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a series of rules that are associated with this Ruleset.
     *
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Attempts to find a Rule by its reference (if it refers to a Sniff).
     *
     * This method does not find Rules that reference another Ruleset. To get those it is recommended to retrieve
     * the entire Ruleset using {@see self::getRules()} and iterate through that array.
     *
     * @return Rule
     */
    public function getRule($ruleRef)
    {
        foreach ($this->rules as $rule) {
            if ($rule->getRef() === $ruleRef) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Adds a new Rule to the list of Rules.
     *
     * @param Rule $rule
     *
     * @return void
     */
    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Returns a Description of this Ruleset.
     *
     * The Description may be used to provide additional information regarding the usage for this Ruleset or the
     * intended audience.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Registers a Description for this Ruleset.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the file/folders (including wildcards) that are to be ignored by the Sniffs.
     *
     * @return string[]
     */
    public function getExcludePatterns()
    {
        return $this->excludePatterns;
    }

    /**
     * Registers which files and folders should not be checked by this Ruleset and child-rulesets.
     *
     * The excluded files and folders may use an asterisk (`*`) to act as a wildcard in a similar way as applies to
     * most filesystems.
     *
     * @param string[] $excludePatterns
     *
     * @return void
     */
    public function setExcludePatterns($excludePatterns)
    {
        $this->excludePatterns = $excludePatterns;
    }

    /**
     * Recursively iterates through every Rule and enables the associated Sniff in the given collection.
     *
     * When a Rule references another Ruleset then we enable all Sniffs that are mentioned in that Ruleset as well and
     * apply the same limitations as in this Ruleset. This includes excluding the paths set in the
     * {@see self::$excludePatterns} property.
     *
     * @param Collection $collection
     *
     * @return void
     */
    public function enableSniffs(Collection $collection)
    {
        foreach ($this->rules as $rule) {
            $ref = $rule->getRef();
            if ($ref instanceof self) {
                $ref->enableSniffs($collection);
            } else {
                $collection->enable($ref);
            }
        }
    }
}
