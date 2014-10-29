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
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Property;

use phpDocumentor\Descriptor\PropertyDescriptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Validates whether a property has a summary, or an `@var` tag with a description.
 */
class HasSummaryValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param PropertyDescriptor $value      The value that should be validated
     * @param Constraint         $constraint The constraint for the validation
     *
     * @throws ConstraintDefinitionException if this is not a constraint on a PropertyDescriptor object.
     */
    public function validate($value, Constraint $constraint)
    {
        if (! $value instanceof PropertyDescriptor) {
            throw new ConstraintDefinitionException(
                'The Property\HasSummary validator may only be used on property objects'
            );
        }

        $var = $value->getVar();
        if (! $value->getSummary() && ($var->count() == 0 || ! current($var->getAll())->getDescription())) {
            $this->context->addViolationAt('summary', $constraint->message, array(), null, null, $constraint->code);
        }
    }
}
