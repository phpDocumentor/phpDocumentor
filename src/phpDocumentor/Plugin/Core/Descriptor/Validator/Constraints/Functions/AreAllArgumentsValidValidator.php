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

        $this->checkParamsExists($arguments, $params);
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

            if ($this->checkArgumentInDocBlock()) {
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
        $isArgumentInDocblockValidator  = new IsArgumentInDocBlockValidator();
        $isArgumentInDocblockValidator->initialize($this->context);

        return $isArgumentInDocblockValidator->validate(
            $this->validationValue,
            new IsArgumentInDocBlock()
        );
    }

    /**
     * Check if argument matches parameter.
     */
    protected function checkArgumentNameMatchParam()
    {
        $doesArgumentNameMatchParamValidator  = new DoesArgumentNameMatchParamValidator;
        $doesArgumentNameMatchParamValidator->initialize($this->context);

        return $doesArgumentNameMatchParamValidator->validate(
            $this->validationValue,
            new DoesArgumentNameMatchParam
        );
    }

    /**
     * Check if argument typehint matches parameter.
     */
    protected function checkArgumentTypehintMatchParam()
    {
        $doesArgumentTypehintMatchParamValidator  = new DoesArgumentTypehintMatchParamValidator;
        $doesArgumentTypehintMatchParamValidator->initialize($this->context);

        return $doesArgumentTypehintMatchParamValidator->validate(
            $this->validationValue,
            new DoesArgumentTypehintMatchParam
        );
    }

    /**
     * Check if parameter exists for argument.
     *
     * @param Collection $params
     * @param Collection $arguments
     */
    protected function checkParamsExists($arguments, $params)
    {
        $this->addValidationValue('arguments', $arguments);
        $this->addValidationValue('params', $params);
        $this->addValidationValue('fqsen', $this->value->getFullyQualifiedStructuralElementName());

        $doesParamsExistsValidator  = new DoesParamsExistsValidator();
        $doesParamsExistsValidator->initialize($this->context);

        return $doesParamsExistsValidator->validate(
            $this->validationValue,
            new DoesParamsExists()
        );
    }
}
