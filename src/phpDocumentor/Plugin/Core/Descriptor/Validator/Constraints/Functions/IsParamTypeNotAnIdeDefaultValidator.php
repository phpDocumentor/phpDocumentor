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
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class IsParamTypeNotAnIdeDefaultValidator extends ConstraintValidator
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
                'The Functions\IsParamTypeNotAnIdeDefault validator may only be used on function or method objects'
            );
        }

        $params = $value->getParam();
        foreach ($params as $param) {
            if ($param instanceof ParamDescriptor) {
                $types = $param->getTypes();
                if (is_array($types)
                    && !empty($types)
                    && (preg_grep('/^.*type$/', $types) || preg_grep('/^.*unknown$/', $types))
                ) {
                    $this->context->addViolationAt(
                        'params',
                        $constraint->message,
                        array($param->getVariableName(), $value->getFullyQualifiedStructuralElementName())
                    );
                }
            }
        }
    }
}
