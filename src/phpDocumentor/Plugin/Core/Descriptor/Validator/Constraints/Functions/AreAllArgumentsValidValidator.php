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
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\IsArgumentInDocblock;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\IsArgumentInDocblockValidator;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\DoesArgumentNameMatchParam;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions\DoesArgumentNameMatchParamValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class AreAllArgumentsValidValidator extends ConstraintValidator
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
                'The Functions\AreAllArgumentsValid validator may only be used on function or method objects'
            );
        }

        $params    = $value->getParam();
        $arguments  = $value->getArguments();
        $argIter = $arguments->getIterator();

        $element['fqsen'] = $value->getFullyQualifiedStructuralElementName();

        foreach($argIter as $argument) {
            $element['key'] = $argIter->key();
            $element['argument'] = $argIter->current();
            $element['params'] = $params;

            $isArgumentInDocblockValidator  = new IsArgumentInDocblockValidator;
            $isArgumentInDocblockConstraint = new IsArgumentInDocblock;
            $result = $isArgumentInDocblockValidator->validate($element, $isArgumentInDocblockConstraint);
            if (is_array($result)) {
                $this->context->addViolationAt('argument', $isArgumentInDocblockConstraint->message, $result);
                continue;
            }

            $element['param'] = $result;

            $doesArgumentNameMatchParamValidator  = new DoesArgumentNameMatchParamValidator;
            $doesArgumentNameMatchParamConstraint = new DoesArgumentNameMatchParam;
            $result = $doesArgumentNameMatchParamValidator->validate($element, $doesArgumentNameMatchParamConstraint);
            if ($result) {
                return $this->context->addViolationAt('argument', $doesArgumentNameMatchParamConstraint->message, $result);
            }

            $doesArgumentTypehintMatchParamValidator  = new DoesArgumentTypehintMatchParamValidator;
            $doesArgumentTypehintMatchParamConstraint = new DoesArgumentTypehintMatchParam;
            $result = $doesArgumentTypehintMatchParamValidator->validate($element, $doesArgumentTypehintMatchParamConstraint);
            if ($result) {
                return $this->context->addViolationAt('argument', $doesArgumentTypehintMatchParamConstraint->message, $result);
            }
        }

        foreach($params as $param) {
            $paramName = $param->getVariableName();

            if ($arguments->offsetGet($paramName)) {
                continue;
            }

            $this->context->addViolationAt('argument', $constraint->message, array($paramName, $element['fqsen']), null, null, 50013);
        }
    }
}
