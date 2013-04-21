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
use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
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
 * Populates the project descriptor using the information retrieved from the
 * Reflection component.
 *
 * @see buildFile() to start digesting information.
 */
class Reflector extends BuilderAbstract
{
    /** @var Validation $validation */
    protected $validation;

    /**
     * Sets the Validation Manager.
     *
     * @param Validation $validation
     *
     * @return void
     */
    public function setValidation(Validation $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Returns the validation manager.
     *
     * @return Validation
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * @param FileReflector $data The reflection of a file.
     *
     * @uses FileDescriptor to create a representation of the file.
     */
    public function buildFile($data)
    {
        $fileDescriptor = new FileDescriptor($data->getHash());
        $fileDescriptor->setLocation($data->getFilename());
        $fileDescriptor->setName(basename($data->getFilename()));

        $this->buildDocBlock($data, $fileDescriptor);

        $packageTagObject = $data->getDocBlock()
            ? reset($data->getDocBlock()->getTagsByName('package'))
            : null;
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
            $constantDescriptor->setLocation($fileDescriptor, $constant->getLineNumber());
            $constantDescriptor->setPackage($fileDescriptor->getPackage());

            $fileDescriptor->getConstants()->set(
                $constantDescriptor->getFullyQualifiedStructuralElementName(),
                $constantDescriptor
            );
        }

        /** @var FunctionReflector $function */
        foreach ($data->getFunctions() as $function) {
            $functionDescriptor = $this->buildFunction($function);
            $functionDescriptor->setLocation($fileDescriptor, $function->getLineNumber());
            $functionDescriptor->setPackage($fileDescriptor->getPackage());

            $fileDescriptor->getFunctions()->set(
                $functionDescriptor->getFullyQualifiedStructuralElementName(),
                $functionDescriptor
            );
        }

        /** @var ClassReflector $class */
        foreach ($data->getClasses() as $class) {
            $classDescriptor = $this->buildClass($class);
            $classDescriptor->setLocation($fileDescriptor, $class->getLineNumber());
            if (!$classDescriptor->getPackage()) {
                $classDescriptor->setPackage($fileDescriptor->getPackage());
            }

            $fileDescriptor->getClasses()->set(
                $classDescriptor->getFullyQualifiedStructuralElementName(),
                $classDescriptor
            );
        }

        /** @var InterfaceReflector $interface */
        foreach ($data->getInterfaces() as $interface) {
            $interfaceDescriptor = $this->buildInterface($interface);
            $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLineNumber());
            if (!$interfaceDescriptor->getPackage()) {
                $interfaceDescriptor->setPackage($fileDescriptor->getPackage());
            }

            $fileDescriptor->getInterfaces()->set(
                $interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                $interfaceDescriptor
            );
        }

        /** @var TraitReflector $trait */
        foreach ($data->getTraits() as $trait) {
            $traitDescriptor = $this->buildTrait($trait);
            $traitDescriptor->setLocation($fileDescriptor, $trait->getLineNumber());
            if (!$traitDescriptor->getPackage()) {
                $traitDescriptor->setPackage($fileDescriptor->getPackage());
            }

            $fileDescriptor->getTraits()->set(
                $traitDescriptor->getFullyQualifiedStructuralElementName(),
                $traitDescriptor
            );
        }

        $this->getProjectDescriptor()->getFiles()->set($fileDescriptor->getPath(), $fileDescriptor);

        // validate the Reflected Information
        $fileDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $fileDescriptor;
    }

    /**
     * Builds a ClassDescriptor and stores it in the Project.
     *
     * @param ClassReflector $data The reflection data to use or this operation.
     *
     * @return ClassDescriptor
     */
    public function buildClass($data)
    {
        $classDescriptor = new ClassDescriptor();

        $classDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $classDescriptor->setName($data->getShortName());

        $this->buildDocBlock($data, $classDescriptor);

        $classDescriptor->setParentClass($data->getParentClass());

        $classDescriptor->setLocation('', $data->getLinenumber());
        $classDescriptor->setAbstract($data->isAbstract());
        $classDescriptor->setFinal($data->isFinal());

        foreach ($data->getInterfaces() as $interfaceClassName) {
            $classDescriptor->getInterfaces()->set($interfaceClassName, $interfaceClassName);
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

        $fqcn = $classDescriptor->getFullyQualifiedStructuralElementName();
        $namespace = substr($fqcn, 0, strrpos($fqcn, '\\'));

        $classDescriptor->setNamespace($namespace);

        // validate the Reflected Information
        $classDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $classDescriptor;
    }

    /**
     * @param InterfaceReflector $data
     */
    public function buildInterface($data)
    {
        $interfaceDescriptor = new InterfaceDescriptor();

        $interfaceDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $interfaceDescriptor->setName($data->getShortName());

        $this->buildDocBlock($data, $interfaceDescriptor);

        $interfaceDescriptor->setLocation('', $data->getLinenumber());

        foreach ($data->getParentInterfaces() as $interfaceClassName) {
            $interfaceDescriptor->getParentInterfaces()->set($interfaceClassName, $interfaceClassName);
        }

        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $interfaceDescriptor);
        }

        $interfaceDescriptor->setNamespace($data->getNamespace());

        // validate the Reflected Information
        $interfaceDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $interfaceDescriptor;
    }

    /**
     * @param TraitReflector $data
     */
    public function buildTrait($data)
    {
        $traitDescriptor = new TraitDescriptor();

        $traitDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $traitDescriptor->setName($data->getShortName());
        $traitDescriptor->setNamespace($data->getNamespace());

        $this->buildDocBlock($data, $traitDescriptor);

        $traitDescriptor->setLocation('', $data->getLinenumber());

        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $traitDescriptor);
        }
        foreach ($data->getProperties() as $property) {
            $this->buildProperty($property, $traitDescriptor);
        }

        // validate the Reflected Information
        $traitDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $traitDescriptor;
    }

    /**
     * @param ClassReflector\ConstantReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor|null $container
     */
    public function buildConstant($data, $container = null)
    {
        $constantDescriptor = new ConstantDescriptor();

        $prefix = ($container)
            ? $container->getFullyQualifiedStructuralElementName() . '::'
            : $data->getNamespace() . '\\';

        $constantDescriptor->setFullyQualifiedStructuralElementName($prefix . $data->getName());
        $constantDescriptor->setName($data->getShortName());
        $constantDescriptor->setValue($data->getValue());
        $constantDescriptor->setNamespace($data->getNamespace());

        $this->buildDocBlock($data, $constantDescriptor);

        if ($container) {
            $constantDescriptor->setParent($container);
            $container->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
        }

        // validate the Reflected Information
        $constantDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $constantDescriptor;
    }

    /**
     * @param FunctionReflector $data
     */
    public function buildFunction($data)
    {
        $functionDescriptor = new FunctionDescriptor();
        $functionDescriptor->setFullyQualifiedStructuralElementName(
            $data->getNamespace() . '\\' . $data->getName() . '()'
        );
        $functionDescriptor->setName($data->getShortName());

        $this->buildDocBlock($data, $functionDescriptor);

        $functionDescriptor->setLocation('', $data->getLinenumber());
        $functionDescriptor->setNamespace($data->getNamespace());

        // validate the Reflected Information
        $functionDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $functionDescriptor;
    }

    /**
     * @param ClassReflector\PropertyReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $container
     */
    public function buildProperty($data, $container)
    {
        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setFullyQualifiedStructuralElementName(
            $container->getFullyQualifiedStructuralElementName() . '::$' . $data->getName()
        );
        $propertyDescriptor->setName($data->getShortName());
        $propertyDescriptor->setVisibility($data->getVisibility());
        $propertyDescriptor->setStatic($data->isStatic());
        $propertyDescriptor->setDefault($data->getDefault());

        // TODO: get Type from @var/@type and set it on the property itself
        // $propertyDescriptor->setType();

        $this->buildDocBlock($data, $propertyDescriptor);

        $propertyDescriptor->setLocation('', $data->getLinenumber());
        $propertyDescriptor->setParent($container);
        $container->getProperties()->set($propertyDescriptor->getName(), $propertyDescriptor);

        // validate the Reflected Information
        $propertyDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $propertyDescriptor;
    }

    /**
     * @param ClassReflector\MethodReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $container
     */
    public function buildMethod($data, $container)
    {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setFullyQualifiedStructuralElementName(
            $container->getFullyQualifiedStructuralElementName() . '::' . $data->getName() . '()'
        );
        $methodDescriptor->setName($data->getShortName());
        $methodDescriptor->setVisibility($data->getVisibility());
        $methodDescriptor->setFinal($data->isFinal());
        $methodDescriptor->setAbstract($data->isAbstract());
        $methodDescriptor->setStatic($data->isStatic());
        $methodDescriptor->setParent($container);

        $this->buildDocBlock($data, $methodDescriptor);

        foreach ($data->getArguments() as $argument) {
            $argumentDescriptor = new ArgumentDescriptor();
            $argumentDescriptor->setName($argument->getName());

            $params = $methodDescriptor->getTags()->get('param', array());

            /** @var ParamDescriptor $tag */
            foreach ($params as $tag) {
                if ($tag->getVariableName() == $argument->getName()) {
                    $argumentDescriptor->setDescription($tag->getDescription());

                    $types = $tag->getTypes() ?: array($argument->getType() ?: 'mixed');
                    $argumentDescriptor->setTypes($types);
                }
            }

            $argumentDescriptor->setDefault($argument->getDefault());
            $methodDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        $methodDescriptor->setLocation('', $data->getLinenumber());
        $container->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);

        // validate the Reflected Information
        $methodDescriptor->setErrors(new Collection($this->getValidation()->validate($data)));

        return $methodDescriptor;
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
                $tagObject = $tagFactory->create($tag);

                $existingTags = $target->getTags()->get($tag->getName(), array());
                $existingTags[] = $tagObject;
                $target->getTags()->set($tag->getName(), $existingTags);
            }
        }
    }
}
