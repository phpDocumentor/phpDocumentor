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

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Generic;

use Psr\Log\LogLevel;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Descriptor\Validator\ValidatorInterface;
use phpDocumentor\Reflection\BaseReflector;

class HasDocBlock implements ValidatorInterface
{
    /**
     *
     *
     * @param BaseReflector $element
     *
     * @return Error|null
     */
    public function validate($element)
    {
        $docBlock = $element->getDocBlock();

        return (null === $docBlock)
            ? new Error(LogLevel::CRITICAL, 'PPC:ERR-50000', $element->getLinenumber(), array($element->getName()))
            : null;
    }
}
