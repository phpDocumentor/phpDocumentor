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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class IsArgumentInDocBlockValidator extends ConstraintValidator
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
                'The Functions\IsArgumentInDocBlock validator may only be used on function or method objects'
            );
        }

        $args = $value->getArguments();

        if ($args->count() > 0) {
            $params = $args->getAll();
            foreach ($params as $param) {
                $type = $param->getTypes();
                if (is_array($type) && empty($type)) {
                    $this->context->addViolationAt(
                        'argument',
                        $constraint->message,
                        array($param->getName(), $value->getFullyQualifiedStructuralElementName())
                    );
                }
            }
        }
    }
}
