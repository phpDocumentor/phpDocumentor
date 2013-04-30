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

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Classes;

use Psr\Log\LogLevel;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Descriptor\Validator\ValidatorInterface;
use phpDocumentor\Reflection\BaseReflector;

class HasSinglePackage implements ValidatorInterface
{
    public function validate($element)
    {
        $docBlock = $element->getDocBlock();
        if (null === $docBlock) {
            throw new \UnexpectedValueException(
                'A DocBlock should be present (and validated) before this validator can be applied'
            );
        }

        if (count($docBlock->getTagsByName('package')) > 1) {
            return new Error(LogLevel::WARNING, 'PPC:ERR-50001', $element->getLinenumber());
        }

        return null;
    }
}
