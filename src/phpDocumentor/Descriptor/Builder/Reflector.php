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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;
use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\TagFactory;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\Validation;
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Builds a Project Descriptor using the information from the Reflection component.
 *
 * The Descriptors are a light representation of the element structure in a project, also known as AST
 * (Abstract Syntax Tree). In order to build this representation from the Reflection API of phpDocumentor
 * can this builder be used.
 *
 * The most straightforward usage is to pass each processed File (in the form of a FileReflector object) to the
 * {@see buildFile()} method. This will extract the file's meta data and child elements (and their child elements),
 * build a FileDescriptor and inject that into the ProjectDescriptor that is created (or passed) as part of this
 * builder (see {@see BuilderAbstract::__construct()}).
 *
 * Example:
 *
 * ```
 * $reflector = new \phpDocumentor\Descriptor\Builder\Reflector();
 * $reflector->buildFile($fileReflector);
 * $projectDescriptor = $reflector->getProjectDescriptor();
 * ```
 *
 * It is also possible to convert each individual element using their respective *build* method but this will not
 * automatically link the element to the correct file.
 *
 * This builder is also capable of validating each Reflector's properties (usually DocBlocks) and populate the *errors*
 * of the linked Descriptor. This only occurs when a Validation manager is supplied, see {@see setValidation()} for
 * more information.
 *
 * @todo Consider moving the individual build* method's contents to mapper classes, they map Reflectors to Descriptors
 */
class Reflector extends BuilderAbstract
{
    /** @var Validation $validation The validation manager that may interpret the Reflectors. */
    protected $validation;

    /**
     * Registers the Validation Manager.
     *
     * @param Validation $validation
     *
     * @see getValidation for a description how validation works for this Builder.
     *
     * @return void
     */
    public function setValidation(Validation $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Returns the Validation Manager.
     *
     * This method allows the caller to retrieve a validation manager that can reflect the provided FileReflectors.
     * It does this by calling the {@see Validation::validate()} method, passing the element that needs to be validated,
     * and provides {@see Error} objects to the linked Descriptor's {@see DescriptorAbstract::$errors} field.
     *
     * @see Validation for more information regarding how validation works and how to register validators.
     * @see setValidation() to register the Validation Manager with this object.
     *
     * @return Validation
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Constructs a FileDescriptor, and child Descriptors, from a FileReflector.
     *
     * This method interprets the provided File Reflector and populates a new FileDescriptor, its child elements and
     * any markers that have been found during the Reflection of a file. If a Validation Manager is provided using the
     * {@see setValidation()} method then this file is also validated and its error collection populated with any
     * violation that was found.
     *
     * @param FileReflector $data The reflection of a file.
     *
     * @see FileReflector for more information regarding the parsing and analysis of a PHP Source File.
     *
     * @return FileDescriptor
     */
    public function buildFile($data)
    {
        $fileDescriptor = new FileDescriptor($data->getHash());
        $fileDescriptor->setLocation($data->getFilename());
        $fileDescriptor->setName(basename($data->getFilename()));

        $this->buildDocBlock($data, $fileDescriptor);

        /** @var DocBlock $docBlock  */
        $docBlock         = $data->getDocBlock();
        $packageTagObject = $docBlock ? reset($docBlock->getTagsByName('package')) : null;

        $fileDescriptor->setSource($data->getContents());
        $fileDescriptor->setPackage($packageTagObject ? $packageTagObject->getDescription() : '');

        $fileDescriptor->setIncludes(new Collection($data->getIncludes()));
        $fileDescriptor->setNamespaceAliases(new Collection($data->getNamespaceAliases()));

        foreach ($data->getMarkers() as $marker) {
            list($type, $message, $line) = $marker;
            $fileDescriptor->getMarkers()->add(
                array(
                    'type'    => $type,
                    'message' => $message,
                    'line'    => $line,
                )
            );
        }

        /** @var ConstantReflector $constant */
        foreach ($data->getConstants() as $constant) {
            $constantDescriptor = $this->buildConstant($constant);
            if ($constantDescriptor) {
                $constantDescriptor->setLocation($fileDescriptor, $constant->getLineNumber());
                $constantDescriptor->setPackage($fileDescriptor->getPackage());

                $fileDescriptor->getConstants()->set(
                    $constantDescriptor->getFullyQualifiedStructuralElementName(),
                    $constantDescriptor
                );
            }
        }

        /** @var FunctionReflector $function */
        foreach ($data->getFunctions() as $function) {
            $functionDescriptor = $this->buildFunction($function);
            if ($functionDescriptor) {
                $functionDescriptor->setLocation($fileDescriptor, $function->getLineNumber());
                $functionDescriptor->setPackage($fileDescriptor->getPackage());

                $this->addToContainer($functionDescriptor, $fileDescriptor->getFunctions());
            }
        }

        /** @var ClassReflector $class */
        foreach ($data->getClasses() as $class) {
            $classDescriptor = $this->buildClass($class);
            if ($classDescriptor) {
                $classDescriptor->setLocation($fileDescriptor, $class->getLineNumber());
                if (!$classDescriptor->getPackage()) {
                    $classDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getClasses()->set(
                    $classDescriptor->getFullyQualifiedStructuralElementName(),
                    $classDescriptor
                );
            }
        }

        /** @var InterfaceReflector $interface */
        foreach ($data->getInterfaces() as $interface) {
            $interfaceDescriptor = $this->buildInterface($interface);
            if ($interfaceDescriptor) {
                $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLineNumber());
                if (!$interfaceDescriptor->getPackage()) {
                    $interfaceDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getInterfaces()->set(
                    $interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                    $interfaceDescriptor
                );
            }
        }

        /** @var TraitReflector $trait */
        foreach ($data->getTraits() as $trait) {
            $traitDescriptor = $this->buildTrait($trait);
            if ($traitDescriptor) {
                $traitDescriptor->setLocation($fileDescriptor, $trait->getLineNumber());
                if (!$traitDescriptor->getPackage()) {
                    $traitDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getTraits()->set(
                    $traitDescriptor->getFullyQualifiedStructuralElementName(),
                    $traitDescriptor
                );
            }
        }

        $this->getProjectDescriptor()->getFiles()->set($fileDescriptor->getPath(), $fileDescriptor);

        // validate the Reflected Information
        if ($this->getValidation()) {
            $fileDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));
        }

        return $fileDescriptor;
    }

    /**
     * Builds a ClassDescriptor using a given Reflector.
     *
     * @param ClassReflector $data The reflection data to use or this operation.
     *
     * @return ClassDescriptor
     */
    public function buildClass($data)
    {
        $assembler = new ClassAssembler();
        $classDescriptor = $assembler->create($data);

        $classDescriptor = $this->filterInternalTag($classDescriptor);
        if (!$classDescriptor) {
            return null;
        }

        foreach ($data->getConstants() as $constant) {
            $this->buildConstant($constant, $classDescriptor);
        }
        foreach ($data->getProperties() as $property) {
            $this->buildProperty($property, $classDescriptor);
        }
        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $classDescriptor);
        }

        $this->validateElement($data, $classDescriptor);

        return $classDescriptor;
    }

    /**
     * Builds a InterfaceDescriptor using a given Reflector.
     *
     * @param InterfaceReflector $data
     *
     * @return InterfaceDescriptor
     */
    public function buildInterface($data)
    {
        $assembler = new InterfaceAssembler();
        $interfaceDescriptor = $assembler->create($data);

        $interfaceDescriptor = $this->filterInternalTag($interfaceDescriptor);

        if ($interfaceDescriptor) {
            foreach ($data->getMethods() as $method) {
                $this->buildMethod($method, $interfaceDescriptor);
            }

            $this->validateElement($data, $interfaceDescriptor);
        }

        return $interfaceDescriptor;
    }

    /**
     * Builds a TraitDescriptor using a given Reflector.
     *
     * @param TraitReflector $data
     *
     * @return TraitDescriptor
     */
    public function buildTrait($data)
    {
        $assembler = new TraitAssembler();
        $traitDescriptor = $assembler->create($data);

        $traitDescriptor = $this->filterInternalTag($traitDescriptor);

        if ($traitDescriptor) {
            foreach ($data->getMethods() as $method) {
                $this->buildMethod($method, $traitDescriptor);
            }
            foreach ($data->getProperties() as $property) {
                $this->buildProperty($property, $traitDescriptor);
            }

            $this->validateElement($data, $traitDescriptor);
        }

        return $traitDescriptor;
    }

    /**
     * Builds a ConstantDescriptor using a given Reflector and links it to a parent class or interface if provided.
     *
     * @param ClassReflector\ConstantReflector|ConstantReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|null           $container
     *
     * @return ConstantDescriptor
     */
    public function buildConstant($data, $container = null)
    {
        $assembler = new ConstantAssembler();
        $constantDescriptor = $assembler->create($data);

        $constantDescriptor->setFullyQualifiedStructuralElementName(
            (($container)
                ? $container->getFullyQualifiedStructuralElementName() . '::'
                : '\\' . $data->getNamespace() . '\\')
            . $constantDescriptor->getFullyQualifiedStructuralElementName()
        );

        $constantDescriptor = $this->filterInternalTag($constantDescriptor);

        if ($constantDescriptor) {
            if ($container) {
                $constantDescriptor->setParent($container);
                $this->addToContainer($constantDescriptor, $container->getConstants());
            }

            $this->validateElement($data, $constantDescriptor);
        }

        return $constantDescriptor;
    }

    /**
     * @param FunctionReflector $data
     */
    public function buildFunction($data)
    {
        $assembler = new FunctionAssembler();
        $functionDescriptor = $assembler->create($data);

        $functionDescriptor = $this->filterInternalTag($functionDescriptor);
        if ($functionDescriptor) {
            $this->validateElement($data, $functionDescriptor);
        }

        return $functionDescriptor;
    }

    /**
     * @param ClassReflector\PropertyReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $container
     */
    public function buildProperty($data, $container)
    {
        $assembler = new PropertyAssembler();
        $propertyDescriptor = $assembler->create($data);

        $propertyDescriptor->setFullyQualifiedStructuralElementName(
            $container->getFullyQualifiedStructuralElementName()
            . '::' . $propertyDescriptor->getFullyQualifiedStructuralElementName()
        );

        $propertyDescriptor = $this->filterInternalTag($propertyDescriptor);
        if ($propertyDescriptor) {
            $this->validateElement($data, $propertyDescriptor);
            $this->addToContainer($propertyDescriptor, $container->getProperties());
        }

        return $propertyDescriptor;
    }

    /**
     * @param ClassReflector\MethodReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $container
     */
    public function buildMethod($data, $container)
    {
        $assembler = new MethodAssembler();
        $methodDescriptor = $assembler->create($data);

        $methodDescriptor->setFullyQualifiedStructuralElementName(
            $container->getFullyQualifiedStructuralElementName()
            . '::' . $methodDescriptor->getFullyQualifiedStructuralElementName()
        );

        $methodDescriptor = $this->filterInternalTag($methodDescriptor);
        if ($methodDescriptor) {
            $this->validateElement($data, $methodDescriptor);
            $this->addToContainer($methodDescriptor, $container->getMethods());
        }

        return $methodDescriptor;
    }

    protected function addToContainer($descriptor, $container)
    {
        $container->set($descriptor->getName(), $descriptor);
    }

    /**
     *
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return DescriptorAbstract| null
     */
    protected function filterInternalTag($descriptor)
    {
        // if internal elements are not allowed; do not add this element
        if ($descriptor->getTags()->get('internal')
            && !$this->isVisibilityAllowed(ProjectDescriptor\Settings::VISIBILITY_INTERNAL)
        ) {
            return null;
        }

        return $descriptor;
    }

    /**
     * @param BaseReflector      $data
     * @param DescriptorAbstract $target
     */
    protected function buildDocBlock($data, $target)
    {
        /** @var DocBlock $docBlock */
        $docBlock = $data->getDocBlock();
        if ($docBlock) {
            $target->setSummary($docBlock->getShortDescription());
            $target->setDescription($docBlock->getLongDescription()->getContents());

            $tagFactory = new TagFactory();

            /** @var Tag $tag */
            foreach ($docBlock->getTags() as $tag) {
                $target->getTags()
                    ->get($tag->getName(), new Collection())
                    ->add($tagFactory->create($tag));
            }
        }
    }

    /**
     *
     *
     * @param $data
     * @param $traitDescriptor
     *
     */
    protected function validateElement($data, $traitDescriptor)
    {
        if ($this->getValidation()) {
            $traitDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));
        }
    }
}
