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

    protected $fqsen;

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

        $this->value = $value;

        $violation = $this->processArgumentValidation($arguments, $params);
        if ($violation) {
            return $violation;
        }

        $this->checkParamsExists($params, $arguments);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function addValidationValue($key, $value)
    {
        $this->validationValue[$key] = $value;

        return $this;
    }

    /**
     * @param Collection $arguments
     * @param Collection $params
     */
    protected function processArgumentValidation($arguments, $params)
    {
        $this->addValidationValue('fqsen', $this->value->getFullyQualifiedStructuralElementName());
        $this->addValidationValue('name', $this->value->getName());

        foreach(array_values($arguments->getAll()) as $key => $argument) {
            $this->addValidationValue('key', $key);
            $this->addValidationValue('argument', $argument);
            $this->addValidationValue('params', $params);
            $this->addValidationValue('index', $key);
            $this->addValidationValue('param', $params[$key]);

            $isArgumentInDocblockValidator  = new IsArgumentInDocBlockValidator();
            $isArgumentInDocblockConstraint = new IsArgumentInDocBlock();

            $value = $isArgumentInDocblockValidator->validate(
                $this->validationValue,
                $isArgumentInDocblockConstraint
            );

            if (is_array($value)) {
                $this->context->addViolationAt(
                    'argument',
                    $isArgumentInDocblockConstraint->message,
                    $value,
                    null,
                    null,
                    $isArgumentInDocblockConstraint->code
                );

                continue;
            }

            if ($this->checkArgumentNameMatchParam()) {
                continue;
            }

            if ($this->checkArgumentTypehintMatchParam()) {
                continue;
            }
        }

        return null;
    }

    /**
     * Check if argument is inside docblock.
     */
    protected function checkArgumentInDocBlock()
    {
        $invalidValue = $isArgumentInDocblockValidator->validate(
            $this->validationValue,
            $isArgumentInDocblockConstraint
        );

        return $invalidValue;
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
            $violation = $this->context->addViolationAt(
                'argument',
                $doesArgumentNameMatchParamConstraint->message,
                $invalidValue,
                null,
                null,
                $doesArgumentNameMatchParamConstraint->code
            );
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
            $violation = $this->context->addViolationAt(
                'argument',
                $doesArgumentTypehintMatchParamConstraint->message,
                $invalidValue,
                null,
                null,
                $doesArgumentTypehintMatchParamConstraint->code
            );
        }

        return $violation;
    }

    /**
     * Check if parameter exists for argument.
     *
     * @param Collection $params
     * @param Collection $arguments
     */
    protected function checkParamsExists($arguments, $params)
    {
        if (count($arguments) > 0) {
            foreach($params as $param) {
                $param = $param->getVariableName();

                if (is_string($param) && $arguments->offsetExists($param)) {
                    continue;
                } elseif ($param instanceof Collection) {
                    foreach ($param as $p) {
                        if (is_string($p) && $arguments->offsetExists($p)) {
                            continue;
                        }
                    }

                    continue;
                }

                $this->context->addViolationAt(
                    'argument',
                    $this->constraint->message,
                    array($paramName, $this->validationValue['fqsen']),
                    null,
                    null,
                    $this->constraint->code
                );
            }
        }
    }
}
