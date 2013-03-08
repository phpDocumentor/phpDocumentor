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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
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

        foreach($data->getMarkers() as $marker) {
            list($type, $message, $line) = $marker;
            $file->getMarkers()->add(array(
                'type'    => $type,
                'message' => $message,
                'line'    => $line,
            ));
        }

        foreach($data->getParseErrors() as $marker) {
            list($type, $message, $line, $code) = $marker;
            $file->getErrors()->add(array(
                'type'    => $type,
                'message' => $message,
                'line'    => $line,
                'code'    => $code,
            ));
        }

        foreach ($data->getConstants() as $constant) {
            $this->buildConstant($constant);
        }
        foreach ($data->getFunctions() as $function) {
            $this->buildFunction($function);
        }
        foreach ($data->getClasses() as $data) {
            $this->buildClass($data);
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

        return $traitDescriptor;
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
        $constant->setNamespace($data->getNamespace());

        $this->buildDocBlock($data, $constant);

        $constant->setLocation('', $data->getLinenumber());

        if ($container) {
            $container->getConstants()->set($constant->getName(), $constant);
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
        $function->setNamespace($data->getNamespace());

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

        foreach($data->getArguments() as $argument) {
            $argumentDescriptor = new ArgumentDescriptor();
            $argumentDescriptor->setName($argument->getName());

            $params = $method->getTags()->get('param', array());

            /** @var ParamDescriptor $tag */
            foreach ($params as $tag) {
                if ($tag->getVariableName() == $argument->getName()) {
                    $argumentDescriptor->setDescription($tag->getDescription());

                    $types = $tag->getTypes() ?: array($argument->getType() ?: 'mixed');
                    $argumentDescriptor->setTypes($types);
                }
            }

            $argumentDescriptor->setDefault($argument->getDefault());
            $method->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

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
                $tagObject = $tagFactory->create($tag);

                $existingTags = $target->getTags()->get($tag->getName(), array());
                $existingTags[] = $tagObject;
                $target->getTags()->set($tag->getName(), $existingTags);
            }
        }
    }
}