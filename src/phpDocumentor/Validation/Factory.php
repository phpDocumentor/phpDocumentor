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


use phpDocumentor\Validation\Rule\Php\DocBlockRequired;
use phpDocumentor\Validation\Rule\Php\Summary;
use phpDocumentor\Validation\Rule\Php\ParamRequired;

final class Factory
{
    public function create($name)
    {
        $ruleSet = new RuleSet($name);
        $ruleSet->addRule(new DocBlockRequired());
        $ruleSet->addRule(new Summary());
        $ruleSet->addRule(new ParamRequired());

        return $ruleSet;
    }
}
