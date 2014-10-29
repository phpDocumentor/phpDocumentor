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
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Plugin\Core\Descriptor\Validator\ValidationValueObject;

class DoesArgumentTypehintMatchParamValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ValidationValueObject) {
            throw new ConstraintDefinitionException(
                'The Functions\DoesArgumentTypehintMatchParam subvalidator may only be used on '
                . ' an array containing a parameter key, a fqsen and an argument object'
            );
        }

        $argument  = $value->argument;
        $parameter = $value->parameter;

        if ($argument instanceof ArgumentDescriptor && $parameter instanceof ParamDescriptor) {
            if (count($argument->getTypes()) === 0
                || in_array(current($argument->getTypes()->getAll()), $parameter->getTypes()->getAll())
            ) {
                return null;
            } elseif (current($argument->getTypes()->getAll()) === 'array'
                && substr(current($parameter->getTypes()->getAll()), -2) == '[]'
            ) {
                return null;
            }

            $this->context->addViolationAt(
                'argument',
                $constraint->message,
                array($argument->getName(), $value->fqsen),
                null,
                null,
                $constraint->code
            );
        }
    }
}
