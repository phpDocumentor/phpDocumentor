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
use phpDocumentor\Plugin\Core\Descriptor\Validator\ValidationValueObject;

class DoesArgumentNameMatchParamValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ValidationValueObject) {
            throw new ConstraintDefinitionException(
                'The Functions\DoesArgumentNameMatchParam subvalidator may only be used on '
                . ' an ValidationValueObject containing a parameter key, a fqsen and an argument object'
            );
        }

        if (empty($value->parameter) || empty($value->argument)) {
            return null;
        }

        $parameterName = $value->parameter->getVariableName();
        $argumentName  = $value->argument->getName();

        if ($parameterName == $argumentName) {
            return null;
        }

        if ($parameterName == '') {
            $value->parameter->setVariableName($argumentName);
            return null;
        }

        $violation = $this->context->addViolationAt(
            'argument',
            $constraint->message,
            array($argumentName, $parameterName, $value->fqsen),
            null,
            null,
            $constraint->code
        );
    }
}
