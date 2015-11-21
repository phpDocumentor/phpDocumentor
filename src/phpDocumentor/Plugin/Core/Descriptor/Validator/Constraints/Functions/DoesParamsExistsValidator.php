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

class DoesParamsExistsValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ValidationValueObject) {
            throw new ConstraintDefinitionException(
                'The Functions\DoesParamsExistsValidator subvalidator may only be used on '
                . ' a ValidationValueObject containing a parameter key, a fqsen and an argument object'
            );
        }

        $arguments  = $value->arguments;
        $parameters = $value->parameters;

        if (count($arguments) > 0 && $parameters instanceof \ArrayAccess) {
            foreach ($parameters as $param) {
                $paramVarName = $param->getVariableName();

                if (empty($paramVarName) || $arguments->offsetExists($paramVarName)) {
                    continue;
                }

                $this->context->addViolationAt(
                    'argument',
                    $constraint->message,
                    array($paramVarName, $value->fqsen),
                    null,
                    null,
                    $constraint->code
                );
            }
        }

        return null;
    }
}
