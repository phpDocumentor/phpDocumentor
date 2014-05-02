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

class DoesParamsExistsValidator extends ConstraintValidator
{
    /**
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_array($value)) {
            throw new ConstraintDefinitionException(
                'The Functions\DoesArgumentNameMatchParam subvalidator may only be used on '
                . ' an array containing a parameter key, a fqsen and an argument object'
            );
        }

        extract($value);
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
                    $constraint->message,
                    array($paramName, $fqsen),
                    null,
                    null,
                    $constraint->code
                );
            }
        }

        return null;
    }
}
