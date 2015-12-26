<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Functions;

use phpDocumentor\Descriptor\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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
            $index = $value->index == '' ? 0 : $value->index;

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
}
