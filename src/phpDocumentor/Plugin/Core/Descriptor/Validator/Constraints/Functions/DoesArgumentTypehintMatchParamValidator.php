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

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions;

use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class DoesArgumentTypehintMatchParamValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_array($value)) {
            throw new ConstraintDefinitionException(
                'The Functions\DoesArgumentTypehintMatchParam subvalidator may only be used on '
                . ' an array containing a parameter key, a fqsen and an argument object'
            );
        }

        extract($value);

        if (count($argument->getTypes()) === 0 || in_array(current($argument->getTypes()), $param->getTypes())) {
            return null;
        } elseif (current($argument->getTypes()) === 'array' && substr(current($param->getTypes()), -2) == '[]') {
            return null;
        }

        return array($argument->getName(), $fqsen);
    }
}
