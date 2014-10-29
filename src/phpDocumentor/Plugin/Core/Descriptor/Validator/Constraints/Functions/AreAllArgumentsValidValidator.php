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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\DoesArgumentNameMatchParam;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\DoesArgumentNameMatchParamValidator;
use phpDocumentor\Plugin\Core\Descriptor\Validator\ValidationValueObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 *
 */
class AreAllArgumentsValidValidator extends ConstraintValidator
{
    /** @var Constraint $constraint */
    protected $constraint;

    /**
     * Value to validate against.
     *
     * @var Collection|null
     */
    protected $validationValue = null;

    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     *
     * @throws ConstraintDefinitionException
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

        $this->initValueObject($value);

        $violation = $this->processArgumentValidation();
        if ($violation) {
            return $violation;
        }

        $this->checkParamsExists();
    }

    protected function initValueObject($value)
    {
        $this->validationValue = new ValidationValueObject();

        $this->validationValue->fqsen = $value->getFullyQualifiedStructuralElementName();
        $this->validationValue->arguments = $value->getArguments();
        $this->validationValue->parameters = $value->getParam();
        $this->validationValue->name = $value->getName();
    }

    /**
     * @return null
     */
    protected function processArgumentValidation()
    {
        $arguments = $this->validationValue->arguments->getAll();

        foreach (array_values($arguments) as $key => $argument) {
            $this->validationValue->key = $key;
            $this->validationValue->argument = $argument;
            $this->validationValue->index = $key;
            $this->validationValue->parameter = isset($this->validationValue->parameters[$key])
                ? $this->validationValue->parameters[$key] : null;

            if ($this->checkArgumentInDocBlock()) {
                continue;
            }

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
        $validator = new IsArgumentInDocBlockValidator();
        $validator->initialize($this->context);

        return $validator->validate($this->validationValue, new IsArgumentInDocBlock);
    }

    /**
     * Check if argument matches parameter.
     */
    protected function checkArgumentNameMatchParam()
    {
        $validator  = new DoesArgumentNameMatchParamValidator;
        $validator->initialize($this->context);

        return $validator->validate($this->validationValue, new DoesArgumentNameMatchParam);
    }

    /**
     * Check if argument typehint matches parameter.
     */
    protected function checkArgumentTypehintMatchParam()
    {
        $validator  = new DoesArgumentTypehintMatchParamValidator;
        $validator->initialize($this->context);

        return $validator->validate($this->validationValue, new DoesArgumentTypehintMatchParam);
    }

    /**
     * Check if parameter exists for argument.
     *
     * @param Collection $params
     * @param Collection $arguments
     */
    protected function checkParamsExists()
    {
        $validator  = new DoesParamsExistsValidator();
        $validator->initialize($this->context);

        return $validator->validate($this->validationValue, new DoesParamsExists);
    }
}
