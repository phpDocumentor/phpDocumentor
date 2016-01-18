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

namespace phpDocumentor\Validation\Rule\Php;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Validation\Result;
use phpDocumentor\Validation\Rule\AbstractRule;
use phpDocumentor\Validation\Rule\Rule;
use phpDocumentor\Validation\Violation;

final class ParamRequired extends AbstractRule
{
    public function validate($element, Result $result)
    {
        if ($element instanceof Method || $element instanceof Function_) {
            if ($element->getDocBlock() !== null) {
                $paramTags = $element->getDocBlock()->getTagsByName('param');

                $documentedParams = [];
                /** @var Param $tag */
                foreach ($paramTags as $tag) {
                    $documentedParams[] = $tag->getVariableName();
                }

                foreach ($element->getArguments() as $argument) {
                    if (!in_array($argument->getName(), $documentedParams)) {
                        $result->addViolation(
                            new Violation(
                                $element->getName(),
                                Rule::SEVERITY_ERROR,
                                static::class,
                                sprintf('%s is missing in docblock', $argument->getName())
                            )
                        );
                    }
                }
            }
        }
    }
}
