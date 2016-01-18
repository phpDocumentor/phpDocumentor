<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Validation;


use phpDocumentor\Validation\Rule\Rule;

final class RuleSet
{
    /**
     * @var null|string
     */
    private $description;

    /**
     * @var Rule
     */
    private $rules;

    /**
     * @var string
     */
    private $name;

    /**
     * RuleSet constructor.
     * @param string $name
     * @param null|string $description
     */
    public function __construct($name, $description = null)
    {
        $this->rules = [];
        $this->description = $description;
        $this->name = $name;
    }

    public function mount(RuleSet $ruleSet)
    {

    }

    public function addRule(Rule $rule)
    {
       $this->rules[] = $rule;
    }

    /**
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }
}
