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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\DoesArgumentNameMatchParam;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\DoesArgumentNameMatchParamValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class AreAllArgumentsValidValidator extends ConstraintValidator
{
    /**
     * @var Constraint $constraint
     */
    protected $constraint;

    /**
     * Value to validate agains.
     * @var array
     */
    protected $validationValue = array();

    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (! $value instanceof MethodDescriptor
            && ! $value instanceof FunctionDescriptor
        ) {
            throw new ConstraintDefinitionException(
                'The Functions\AreAllArgumentsValid validator may only be used on function or method objects'
            );
        }

        $this->constraint = $constraint;

        $params = $value->getParam();
        $arguments  = $value->getArguments();

        $violation = $this->processArgumentValidation();
        if ($violation) {
            return $violation;
        }

        $this->checkParamsExists($params, $arguments);
    }

    /**
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    protected function addValidationValue($key, $value)
    {
        $this->validationValue[$key] = $value;
    }

    /**
     * @param Collection $arguments
     * @param Collection $params
     */
    protected function processArgumentValidation(Collection $arguments, Collection $params)
    {
        $argIter = $arguments->getIterator();

        $this->addValidationValue('fqsen', $value->getFullyQualifiedStructuralElementName());

        foreach($argIter as $argument) {
            $this->addValidationValue('key', $argIter->key());
            $this->addValidationValue('argument', $argIter->current());
            $this->addValidationValue('params', $params);

            $value = $this->checkArgumentInDocBlock();

            if (is_array($value)) {
                $this->context->addViolationAt(
                    'argument',
                    $this->isArgumentInDocblockConstraint->message,
                    $value
                );

                continue;
            }
            $this->addValidationValue('param', $value);

            $violation = $this->checkArgumentNameMatchParam();
            if ($violation) {
                return $violation;
            }

            $violation = $this->checkArgumentTypehintMatchParam();
            if ($violation) {
                return $violation;
            }
        }

        return null;
    }

    /**
     * Check if argument is inside docblock.
     */
    protected function checkArgumentInDocBlock()
    {
        $isArgumentInDocblockValidator  = new IsArgumentInDocBlockValidator();
        $this->isArgumentInDocblockConstraint = new IsArgumentInDocBlock();

        return $isArgumentInDocblockValidator->validate(
            $this->validationValue,
            $this->isArgumentInDocblockConstraint
        );
    }

    /**
     * Check if argument matches parameter.
     */
    protected function checkArgumentNameMatchParam()
    {
        $doesArgumentNameMatchParamValidator  = new DoesArgumentNameMatchParamValidator;
        $doesArgumentNameMatchParamConstraint = new DoesArgumentNameMatchParam;

        $invalidValue = $doesArgumentNameMatchParamValidator->validate(
            $this->validationValue,
            $doesArgumentNameMatchParamConstraint
        );

        $violation = null;

        if ($invalidValue) {
            $violation = $this->context->addViolationAt('argument', $doesArgumentNameMatchParamConstraint->message, $invalidValue);
        }

        return $violation;
    }

    /**
     * Check if argument typehint matches parameter.
     */
    protected function checkArgumentTypehintMatchParam()
    {
        $doesArgumentTypehintMatchParamValidator  = new DoesArgumentTypehintMatchParamValidator;
        $doesArgumentTypehintMatchParamConstraint = new DoesArgumentTypehintMatchParam;

        $invalidValue = $doesArgumentTypehintMatchParamValidator->validate(
            $this->validationValue,
            $doesArgumentTypehintMatchParamConstraint
        );

        $violation = null;

        if ($invalidValue) {
            $violation = $this->context->addViolationAt('argument', $doesArgumentTypehintMatchParamConstraint->message, $invalidValue);
        }

        return $violation;
    }

    /**
     * Check if parameter exists for argument.
     *
     * @param Collection $params
     * @param Collection $arguments
     */
    protected function checkParamsExists(Collection $arguments, Collection $params)
    {
        foreach($params as $param) {
            $paramName = $param->getVariableName();

            if ($arguments->offsetGet($paramName)) {
                continue;
            }

            $this->context->addViolationAt(
                'argument',
                $this->constraint->message,
                array($paramName, $this->validationValue['fqsen']),
                null,
                null,
                50013
            );
        }
    }
}
