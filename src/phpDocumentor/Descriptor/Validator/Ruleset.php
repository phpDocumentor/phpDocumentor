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
     * @param string $name
     * @param Rule[] $rules
     */
    public function __construct($name, array $rules = array())
    {
        $this->rules = $rules;
        $this->name  = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return Rule
     */
    public function getRule($ruleRef)
    {
        return isset($this->rules[$ruleRef]) ? $this->rules[$ruleRef] : null;
    }

    /**
     * @param Rule $rule
     *
     * @return void
     */
    public function addRule(Rule $rule)
    {
        $this->rules[$rule->getRef()] = $rule;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getExcludePatterns()
    {
        return $this->excludePatterns;
    }

    /**
     * @param array $excludePatterns
     */
    public function setExcludePatterns($excludePatterns)
    {
        $this->excludePatterns = $excludePatterns;
    }

    public function enableValidations(Collection $collection)
    {
        foreach ($this->rules as $rule) {
            $ref = $rule->getRef();
            if ($ref instanceof self) {
                $ref->enableValidations($collection);
            } else {
                $collection->enable($ref);
            }
        }
    }
}
