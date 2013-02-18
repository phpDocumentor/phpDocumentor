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

use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\Tag\TagFactory;

use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;

class Reflector extends BuilderAbstract
{
    /**
     * @param FileReflector $data
     */
    public function buildFile($data)
    {
        $file = new FileDescriptor($data->getHash());
        $file->setLocation($data->getFilename());
        $file->setName(basename($data->getFilename()));

        $this->buildDocBlock($data, $file);

        $file->setSource($data->getContents());

        $file->setIncludes(new Collection($data->getIncludes()));
        $file->setNamespaceAliases(new Collection($data->getNamespaceAliases()));
        $file->setErrors(new Collection($data->getParseErrors()));

        foreach ($data->getConstants() as $constant) {
            $this->buildConstant($constant);
        }
        foreach ($data->getFunctions() as $function) {
            $this->buildFunction($function);
        }
        foreach ($data->getClasses() as $classReflector) {
            $this->buildClass($classReflector);
        }
        foreach ($data->getInterfaces() as $interface) {
            $this->buildInterface($interface);
        }
        foreach ($data->getTraits() as $trait) {
            $this->buildTrait($trait);
        }

        $this->getProjectDescriptor()->getFiles()->set($file->getPath(), $file);

        return $file;
    }

    /**
     * Builds a ClassDescriptor and stores it in the Project.
     *
     * This method scans the classes index whether a descriptor already exists in the project, for example when
     * placeholders are used during inheritance building, and if so, hydrates an existing descriptor.
     *
     * If no descriptor is found, than a new descriptor is created.
     *
     * @param ClassReflector $data The reflection data to use or this operation.
     *
     * @return ClassDescriptor
     */
    public function buildClass($data)
    {
        $classes = $this->project->getIndexes()->get('classes', new Collection());
        $classDescriptor = $classes->get($data->getName());

        $classDescriptor = $this->hydrateClassDescriptor($classDescriptor, $data);

        $this->storeClassDescriptor($classDescriptor);

        return $classDescriptor;
    }

    /**
     * @param ClassReflector $classReflector
     */
    protected function hydrateClassDescriptor($classDescriptor, $classReflector)
    {
        $classDescriptor = $classDescriptor ?: new ClassDescriptor();

        $classDescriptor->setFullyQualifiedStructuralElementName($classReflector->getName());
        $classDescriptor->setName($classReflector->getShortName());

        $this->buildDocBlock($classReflector, $classDescriptor);

        $parentClassName = $classReflector->getParentClass();
        $parentDescriptor = $parentClassName
            ? $this->findOrCreateIndexItemForElement(
                'classes',
                $parentClassName,
                new ClassDescriptor()
            )
            : null;

        $classDescriptor->setParentClass($parentDescriptor);

        $classDescriptor->setLocation('', $classReflector->getLinenumber());
        $classDescriptor->setAbstract($classReflector->isAbstract());
        $classDescriptor->setFinal($classReflector->isFinal());

        foreach ($classReflector->getInterfaces() as $interfaceClassName) {
            $interfaceDescriptor = $this->findOrCreateIndexItemForElement(
                'interfaces',
                $interfaceClassName,
                new InterfaceDescriptor()
            );

            $classDescriptor->getInterfaces()->set($interfaceClassName, $interfaceDescriptor);
        }

        foreach ($classReflector->getConstants() as $constant) {
            $this->buildConstant($constant, $classDescriptor);
        }
        foreach ($classReflector->getProperties() as $property) {
            $this->buildProperty($property, $classDescriptor);
        }
        foreach ($classReflector->getMethods() as $method) {
            $this->buildMethod($method, $classDescriptor);
        }

        return $classDescriptor;
    }

    /**
     * @param $indexKey
     * @param $parentClassName
     * @param $newClassDescriptor
     * @return \phpDocumentor\Descriptor\ClassDescriptor
     */
    protected function findOrCreateIndexItemForElement($indexKey, $parentClassName, $newClassDescriptor)
    {
        $classes = $this->project->getIndexes()->get($indexKey, new Collection());

        /** @var ClassDescriptor $parentDescriptor */
        $parentDescriptor = $classes->get($parentClassName, $newClassDescriptor);
        if (!$parentDescriptor->getFullyQualifiedStructuralElementName()) {
            // on a new descriptor, set the name and store it in the structure
            $parentDescriptor->setFullyQualifiedStructuralElementName($parentClassName);
            $parentDescriptor->setName(substr($parentClassName, strrpos($parentClassName, '\\') + 1));

            $this->storeClassDescriptor($parentDescriptor);
            return $parentDescriptor;
        }
        return $parentDescriptor;
    }

    /**
     * Stores the given Class Descriptor in the correct namespace and in the 'classes' index.
     *
     * @param ClassDescriptor $classDescriptor
     *
     * @return void
     */
    protected function storeClassDescriptor($classDescriptor)
    {
        $fqcn = $classDescriptor->getFullyQualifiedStructuralElementName();
        $namespace = substr($fqcn, 0, strrpos($fqcn, '\\'));
        $this->locateNamespace($namespace)
            ->getClasses()->set($classDescriptor->getName(), $classDescriptor);

        /** @var Collection $classes */
        $classes = $this->project->getIndexes()->get('classes', new Collection());
        $classes->set($fqcn, $classDescriptor);
    }

    /**
     * @param InterfaceReflector $data
     */
    public function buildInterface($data)
    {
        $interfaces = $this->project->getIndexes()->get('interfaces', new Collection());
        $interfaceDescriptor = $interfaces->get($data->getName(), new InterfaceDescriptor());

        $interfaceDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $interfaceDescriptor->setName($data->getShortName());

        $this->buildDocBlock($data, $interfaceDescriptor);

        $interfaceDescriptor->setLocation('', $data->getLinenumber());

        foreach ($data->getParentInterfaces() as $interfaceClassName) {
            $interfaceDescriptor = $this->findOrCreateIndexItemForElement(
                'interfaces',
                $interfaceClassName,
                new InterfaceDescriptor()
            );

            $interfaceDescriptor->getParentInterfaces()->set($interfaceClassName, $interfaceDescriptor);
        }

        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $interfaceDescriptor);
        }

        $this->storeInterfaceDescriptor($interfaceDescriptor);

        return $interfaceDescriptor;
    }

    /**
     * @param $interface
     */
    protected function storeInterfaceDescriptor($interface)
    {
        $fqcn = $interface->getFullyQualifiedStructuralElementName();
        $namespace = substr($fqcn, 0, strrpos($fqcn, '\\'));
        $this->locateNamespace($namespace)
            ->getInterfaces()->set($interface->getName(), $interface);

        /** @var Collection $interfaces */
        $interfaces = $this->project->getIndexes()->get('interfaces', new Collection());
        $interfaces->set($interface->getFullyQualifiedStructuralElementName(), $interface);
    }

    /**
     * @param TraitReflector $data
     */
    public function buildTrait($data)
    {
        $traits = $this->project->getIndexes()->get('traits', new Collection());
        $traitDescriptor = $traits->get($data->getName(), new TraitDescriptor());

        $traitDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $traitDescriptor->setName($data->getShortName());

        $this->buildDocBlock($data, $traitDescriptor);

        $traitDescriptor->setLocation('', $data->getLinenumber());

        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $traitDescriptor);
        }
        foreach ($data->getProperties() as $property) {
            $this->buildProperty($property, $traitDescriptor);
        }

        $this->storeTraitDescriptor($traitDescriptor);

        return $traitDescriptor;
    }

    /**
     * @param $trait
     */
    protected function storeTraitDescriptor($trait)
    {
        $fqcn = $trait->getFullyQualifiedStructuralElementName();
        $namespace = substr($fqcn, 0, strrpos($fqcn, '\\'));
        $this->locateNamespace($namespace)
            ->getClasses()->set($trait->getName(), $trait);

        /** @var Collection $traits */
        $traits = $this->project->getIndexes()->get('traits', new Collection());
        $traits->set($trait->getFullyQualifiedStructuralElementName(), $trait);
    }

    /**
     * @param ClassReflector\ConstantReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor|null $container
     */
    public function buildConstant($data, $container = null)
    {
        $constant = new ConstantDescriptor();

        $prefix = ($container)
            ? $container->getFullyQualifiedStructuralElementName() . '::'
            : $data->getNamespace() . '\\';

        $constant->setFullyQualifiedStructuralElementName($prefix . $data->getName());
        $constant->setName($data->getShortName());
        $constant->setValue($data->getValue());

        $this->buildDocBlock($data, $constant);

        $constant->setLocation('', $data->getLinenumber());

        if ($container) {
            $container->getConstants()->set($constant->getName(), $constant);
        } else {
            $this->locateNamespace($data->getNamespace())->getConstants()->set($constant->getName(), $constant);
        }

        return $constant;
    }

    /**
     * @param FunctionReflector $data
     */
    public function buildFunction($data)
    {
        $function = new FunctionDescriptor();
        $function->setFullyQualifiedStructuralElementName($data->getNamespace() . '\\' . $data->getName() . '()');
        $function->setName($data->getShortName());

        $this->buildDocBlock($data, $function);

        $function->setLocation('', $data->getLinenumber());
        $this->locateNamespace($data->getNamespace())
            ->getFunctions()->set($function->getName(), $function);

        return $function;
    }

    /**
     * @param ClassReflector\PropertyReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $container
     */
    public function buildProperty($data, $container)
    {
        $property = new PropertyDescriptor();
        $property->setFullyQualifiedStructuralElementName(
            $container->getFullyQualifiedStructuralElementName() . '::$' . $data->getName()
        );
        $property->setName($data->getShortName());
        $property->setVisibility($data->getVisibility());
        $property->setStatic($data->isStatic());
        $property->setDefault($data->getDefault());
//        $property->setType();

        $this->buildDocBlock($data, $property);

        $property->setLocation('', $data->getLinenumber());
        $container->getProperties()->set($property->getName(), $property);

        return $property;
    }

    /**
     * @param ClassReflector\MethodReflector $data
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $container
     */
    public function buildMethod($data, $container)
    {
        $method = new MethodDescriptor();
        $method->setFullyQualifiedStructuralElementName(
            $container->getFullyQualifiedStructuralElementName() . '::' . $data->getName() . '()'
        );
        $method->setName($data->getShortName());
        $method->setVisibility($data->getVisibility());
        $method->setFinal($data->isFinal());
        $method->setAbstract($data->isAbstract());
        $method->setStatic($data->isStatic());

        $this->buildDocBlock($data, $method);

        $method->setLocation('', $data->getLinenumber());
        $container->getMethods()->set($method->getName(), $method);

        return $method;
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
                $tagObject = $tagFactory->create($tag->getName(), $tag->getDescription());
                $existingTags = $target->getTags()->get($tag->getName(), array());
                $existingTags[] = $tagObject;
                $target->getTags()->set($tag->getName(), $existingTags);
            }
        }
    }

    /**
     * Finds the Namespace object in the namespaces tree that matches the given string and creates new namespace where
     * none are found.
     *
     * @param string $namespace
     *
     * @return NamespaceDescriptor
     */
    protected function locateNamespace($namespace)
    {
        $namespace_name = '';
        $namespaceDescriptor = $this->getProjectDescriptor()->getNamespace();
        foreach (explode('\\', ltrim($namespace, '\\')) as $part) {
            $namespace_name .= '\\' . $part;
            if ($namespaceDescriptor->getNamespaces()->$part) {
                $namespaceDescriptor = $namespaceDescriptor->getNamespaces()->$part;
                continue;
            }

            $new_namespace = new NamespaceDescriptor();
            $new_namespace->setName($part);
            $new_namespace->setFullyQualifiedStructuralElementName($namespace_name);
            $namespaceDescriptor->getNamespaces()->set($part, $new_namespace);

            $namespaceDescriptor = $new_namespace;
        }

        return $namespaceDescriptor;
    }
}