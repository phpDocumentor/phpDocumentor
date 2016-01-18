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
use phpDocumentor\Validation\Violation;

final class Summary extends AbstractRule
{
    /**
     * @param object $element
     * @param Result $result
     */
    public function validate($element, Result $result)
    {
        $elementName = method_exists($element, 'getName') ? $element->getName() : $element->getPath();

        if ($element->getDocBlock() !== null) {
            if ($element->getDocBlock()->getSummary() === null) {
                $result->addViolation(new Violation($elementName, $this->severity, static::class, 'Missing summary'));
            }
        }
    }
}
