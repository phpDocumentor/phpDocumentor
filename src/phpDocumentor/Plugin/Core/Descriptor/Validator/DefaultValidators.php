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

namespace phpDocumentor\Plugin\Core\Descriptor\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints as phpDocAssert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultValidators
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    public function __invoke()
    {
        /** @var ClassMetadata $fileMetadata */
        $fileMetadata  = $this->validator->getMetadataFor('phpDocumentor\Descriptor\FileDescriptor');
        $this->validator->getMetadataFor('phpDocumentor\Descriptor\ConstantDescriptor');
        /** @var ClassMetadata $functionMetadata */
        $functionMetadata  = $this->validator->getMetadataFor('phpDocumentor\Descriptor\FunctionDescriptor');
        /** @var ClassMetadata $classMetadata */
        $classMetadata     = $this->validator->getMetadataFor('phpDocumentor\Descriptor\ClassDescriptor');
        /** @var ClassMetadata $interfaceMetadata */
        $interfaceMetadata = $this->validator->getMetadataFor('phpDocumentor\Descriptor\InterfaceDescriptor');
        /** @var ClassMetadata $traitMetadata */
        $traitMetadata     = $this->validator->getMetadataFor('phpDocumentor\Descriptor\TraitDescriptor');
        /** @var ClassMetadata $propertyMetadata */
        $propertyMetadata  = $this->validator->getMetadataFor('phpDocumentor\Descriptor\PropertyDescriptor');
        /** @var ClassMetadata $methodMetadata */
        $methodMetadata    = $this->validator->getMetadataFor('phpDocumentor\Descriptor\MethodDescriptor');

        $fileMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50000')));
        $classMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50005')));
        $propertyMetadata->addConstraint(new phpDocAssert\Property\HasSummary());
        $methodMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50008')));
        $interfaceMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50009')));
        $traitMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50010')));
        $functionMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50011')));

        $functionMetadata->addConstraint(new phpDocAssert\Functions\IsReturnTypeNotAnIdeDefault());
        $methodMetadata->addConstraint(new phpDocAssert\Functions\IsReturnTypeNotAnIdeDefault());

        $functionMetadata->addConstraint(new phpDocAssert\Functions\IsParamTypeNotAnIdeDefault());
        $methodMetadata->addConstraint(new phpDocAssert\Functions\IsParamTypeNotAnIdeDefault());

        $functionMetadata->addConstraint(new phpDocAssert\Functions\IsArgumentInDocBlock());
        $methodMetadata->addConstraint(new phpDocAssert\Functions\IsArgumentInDocBlock());

        $classMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());
        $interfaceMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());
        $traitMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());
        $fileMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());

        $classMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());
        $interfaceMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());
        $traitMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());
        $fileMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());

        $classMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
        $interfaceMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
        $traitMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
        $fileMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
    }
}
