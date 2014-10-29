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
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Validates whether a file, class, interface or trait has not more than 1 package tags.
 */
class HasSinglePackageValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param FileDescriptor|ClassDescriptor|InterfaceDescriptor|TraitDescriptor $value      The value that should
     *     be validated.
     * @param Constraint                                                         $constraint The constraint for
     *     the validation.
     *
     * @throws ConstraintDefinitionException if this is not a constraint on a PropertyDescriptor object.
     */
    public function validate($value, Constraint $constraint)
    {
        if (! $value instanceof FileDescriptor
            && ! $value instanceof ClassDescriptor
            && ! $value instanceof InterfaceDescriptor
            && ! $value instanceof TraitDescriptor
        ) {
            throw new ConstraintDefinitionException(
                'The HasSinglePackage validator may only be used on files, classes, interfaces and traits'
            );
        }

        if ($value->getTags()->get('package', new Collection())->count() > 1) {
            $this->context->addViolationAt('package', $constraint->message, array(), null, null, $constraint->code);
        }
    }
}
