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

use phpDocumentor\Validation\Result;
use phpDocumentor\Validation\Rule\AbstractRule;
use phpDocumentor\Validation\Rule\Rule;
use phpDocumentor\Validation\Violation;

final class DocBlockRequired extends AbstractRule
{

    public function validate($element, Result $result)
    {
        if ($element->getDocBlock() === null) {
            $elementName = method_exists($element, 'getFqsen') ? $element->getFqsen() : $element->getPath();

            $result->addViolation(
                new Violation(
                    $elementName,
                    Rule::SEVERITY_ERROR,
                    1,
                    sprintf('%s is missing required docblock', $elementName)
                )
            );
        }
    }
}
