<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use phpDocumentor\Plugin\Core\Descriptor\Validator\ValidationValueObject;

/**
 * Validates if a Method or Function's arguments all have an accompanying param tag in the DocBlock.
 */
class IsArgumentInDocBlockValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof ValidationValueObject && count($value->argument) > 0) {
            $argument = $value->argument;
            /* @var $params \phpDocumentor\Descriptor\Collection */
            $params   = $value->parameters;
            $index    = (int) $value->index;

            if ($params && $params->offsetExists($index)) {
                return null;
            }

            $this->context->addViolationAt(
                'argument',
                $constraint->message,
                array($argument->getName(), $value->name),
                null,
                null,
                $constraint->code
            );
        }
    }

    /**
     * Returns whether the list of param tags features the given argument.
     *
     * @param ParamDescriptor[]|Collection $parameters
     * @param ArgumentDescriptor           $argument
     *
     * @return boolean
     */
    private function existsParamTagWithArgument($parameters, ArgumentDescriptor $argument)
    {
        foreach ($parameters as $parameter) {
            if ($argument->getName() == $parameter->getVariableName()) {
                return true;
            }
        }

        return false;
    }
}
