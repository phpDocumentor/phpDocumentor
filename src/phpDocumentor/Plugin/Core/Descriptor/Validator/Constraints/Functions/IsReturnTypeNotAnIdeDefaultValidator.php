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

use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class IsReturnTypeNotAnIdeDefaultValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (! $value instanceof MethodDescriptor
            && ! $value instanceof FunctionDescriptor
        ) {
            throw new ConstraintDefinitionException(
                'The Functions\IsReturnTypeNotAnIdeDefault validator may only be used on function or method objects'
            );
        }

        $return = $value->getResponse();
        if ($return instanceof ReturnDescriptor) {
            $types = $return->getTypes();
            if (is_array($types) && !empty($types) && preg_grep('/^.*type$/', $types)) {
                $this->context->addViolationAt(
                    'response',
                    $constraint->message,
                    array($return->getName(), $value->getFullyQualifiedStructuralElementName()),
                    null,
                    null,
                    $constraint->code
                );
            }
        }
    }
}
