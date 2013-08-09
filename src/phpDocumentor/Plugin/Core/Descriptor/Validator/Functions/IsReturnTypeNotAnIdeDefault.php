<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Functions;

use phpDocumentor\Descriptor\Validator\Error;
use Psr\Log\LogLevel;

class IsReturnTypeNotAnIdeDefault
{
    public function validate($element)
    {
        $docBlock = $element->getDocBlock();
        if (null === $docBlock) {
            throw new \UnexpectedValueException(
                'A DocBlock should be present (and validated) before this validator can be applied'
            );
        }

        if ($docBlock->hasTag('return')) {
            $returnTag = current($docBlock->getTagsByName('return'));
            if ($returnTag->getType() == 'type') {
                return new Error(
                    LogLevel::WARNING,
                    'PPC:ERR-50017',
                    $element->getLinenumber(),
                    array('return', $element->getName())
                );
            }
        }

        return null;
    }
}
