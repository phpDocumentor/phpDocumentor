<?php
namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\Tag\TagFactory;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;

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
        foreach ($data->getClasses() as $class) {
            $classObject = $this->buildClass($class);

            /** @var Collection $classes  */
            $classes = $this->project->getIndexes()->get('classes', new Collection());
            $classes->add($classObject->getFullyQualifiedStructuralElementName());
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
     * @param ClassReflector $data
     */
    public function buildClass($data)
    {
        $class = new ClassDescriptor();
        $class->setFullyQualifiedStructuralElementName($data->getName());
        $class->setName($data->getShortName());

        $this->buildDocBlock($data, $class);

        $class->setLocation('', $data->getLinenumber());
        $class->setAbstract($data->isAbstract());
        $class->setFinal($data->isFinal());
        $class->setParentClass($data->getParentClass());
        $class->setInterfaces(new Collection($data->getInterfaces()));

        foreach ($data->getConstants() as $constant) {
            $this->buildConstant($constant, $class);
        }
        foreach ($data->getProperties() as $property) {
            $this->buildProperty($property, $class);
        }
        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $class);
        }

        $this->locateNamespace($data)
            ->getClasses()->set($class->getName(), $class);

        return $class;
    }

    /**
     * @param InterfaceReflector $data
     */
    public function buildInterface($data)
    {
        $interface = new InterfaceDescriptor();
        $interface->setFullyQualifiedStructuralElementName($data->getName());
        $interface->setName($data->getShortName());

        $this->buildDocBlock($data, $interface);

        $interface->setLocation('', $data->getLinenumber());
        $interface->setParentInterfaces(new Collection($data->getParentInterfaces()));

        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $interface);
        }

        $this->locateNamespace($data)
            ->getInterfaces()->set($interface->getName(), $interface);

        return $interface;
    }

    /**
     * @param TraitReflector $data
     */
    public function buildTrait($data)
    {
        $trait = new TraitDescriptor();
        $trait->setFullyQualifiedStructuralElementName($data->getName());
        $trait->setName($data->getShortName());

        $this->buildDocBlock($data, $trait);

        $trait->setLocation('', $data->getLinenumber());

        foreach ($data->getMethods() as $method) {
            $this->buildMethod($method, $trait);
        }
        foreach ($data->getProperties() as $property) {
            $this->buildProperty($property, $trait);
        }

        $this->locateNamespace($data)
            ->getClasses()->set($trait->getName(), $trait);

        return $trait;
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
            $this->locateNamespace($data)->getConstants()->set($constant->getName(), $constant);
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
        $this->locateNamespace($data)
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
     * @param BaseReflector $data
     *
     * @return NamespaceDescriptor
     */
    protected function locateNamespace($data)
    {
        $namespace_name = '';
        $namespace = $this->getProjectDescriptor()->getNamespace();
        foreach (explode('\\', ltrim($data->getNamespace(), '\\')) as $part) {
            $namespace_name .= '\\' . $part;
            if ($namespace->getNamespaces()->$part) {
                $namespace = $namespace->getNamespaces()->$part;
                continue;
            }

            $new_namespace = new NamespaceDescriptor();
            $new_namespace->setName($part);
            $new_namespace->setFullyQualifiedStructuralElementName($namespace_name);
            $namespace->getNamespaces()->set($part, $new_namespace);

            $namespace = $new_namespace;
        }

        return $namespace;
    }
}