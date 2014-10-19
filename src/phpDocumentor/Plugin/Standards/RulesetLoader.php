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
 * Class responsible for loading a ruleset based on its name or a path leading to a Ruleset.
 *
 * @todo add loading from file
 * @todo add referencing another Ruleset, including by path, and importing that in a Rule.
 */
final class RulesetLoader
{
    /** @var Collection */
    private $sniffs;

    /** @var Ruleset[] */
    private $rulesets;

    /**
     * Initializes this object with a set of Sniffs and the available Rulesets.
     *
     * Because both the collection of sniffs and rulesets are objects we can add Rulesets and Sniffs after this
     * object is instantiated and use those to load the desired Ruleset in the method {@see self::load()}.
     *
     * @param Collection   $sniffs
     * @param \ArrayObject $rulesets
     */
    public function __construct(Collection $sniffs, \ArrayObject $rulesets)
    {
        $this->sniffs   = $sniffs;
        $this->rulesets = $rulesets;
    }

    /**
     * Loads a Ruleset with the given name.
     *
     * @param string $rulesetName
     *
     * @throws \InvalidArgumentException if a ruleset with the given name does not exist.
     *
     * @return Ruleset
     */
    public function load($rulesetName)
    {
        if (!isset($this->rulesets[$rulesetName])) {
            throw new \InvalidArgumentException('Ruleset with the name "' . $rulesetName . '" was not found');
        }

        $ruleset = $this->rulesets[$rulesetName];
        $ruleset->enableSniffs($this->sniffs);

        return $ruleset;
    }
}