<?php

namespace phpDocumentor\Application\Renderer;

use phpDocumentor\Application\Renderer\Template\Action\Jsonp;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\ReadModel\ReadModel;
use phpDocumentor\DomainModel\Renderer\Template\Action;

/**
 * Writes the collected data as a series of JSONP files.
 *
 * This writer will create a series of JSONP files that represent all of the data collected by phpDocumentor on a
 * project. These JSONP files can then be exposed by an API, consumed by a Javascript framework or otherwise be
 * re-used.
 *
 * The original reason for creating this writer was to use it to fuel a Javascript framework based template and as
 * such some assumptions have been done in the layout that help with that goal. One of those has been the choice for
 * JSONP opposed to JSON as JSONP makes it possible to open the JSON content from a local file (`file://`) without
 * having issues with Cross-domain requests.
 *
 * Because these are static pre-generated files the callback name is not configurable via a query parameter $callback
 * as is usually the case but a fixed callback name is used for each individual file.
 *
 * The following files are generated:
 *
 * namespaces.json (callback name: `namespaces`)
 *     Contains a tree structure of all namespaces and has a listing of all child elements. Constants
 *     and functions have their complete contents included but classes, interfaces and traits only have
 *     an FQCN listed so that you can refer to another JSONP file.
 *
 * packages.json (callback name: `packages`)
 *     Contains a tree structure of all packages and has a listing of all child elements. Constants
 *     and functions have their complete contents included but classes, interfaces and traits only have
 *     an FQCN listed so that you can refer to another JSONP file.
 *
 * files/*.json (callback name: `fileDefinition`)
 *     The subfolder `files` will contain a JSONP file for each file in a project. This file contains a listing of all
 *     child elements. Constants and functions have their complete contents included but classes, interfaces and
 *     traits only have an FQCN listed so that you can refer to another JSONP file.
 *
 * classes/*.json (callback name: `classDefinition`)
 *     The subfolder `classes` will contain a JSONP file for each class, interface and trait in a project. Each file
 *     contains a listing of all properties and child elements for a class, interface or trait. This includes a
 *     member `type` that can be either 'class', 'interface' or 'trait' to distinguish between the three types
 *     of 'classes'.
 */
class JsonpRenderer
{
    public function render(ReadModel $view, Path $destination, $template = null)
    {
        // TODO: Implement render() method.
    }

    /**
     * Generate a series of JSONP files based on the ProjectDescriptor's structure in the target directory.
     *
     * This method is responsible for writing a series of JSONP files to disk in the directory specified by the
     * user when starting phpDocumentor. A complete description of what is generated can be found in the documentation
     * of this class itself.
     *
     * @param Jsonp $action
     *
     * @return void
     */
    public function handle(Action $action)
    {
        $project = $this->analyzer->getProjectDescriptor();
        $folder = $action->getRenderPass()->getDestination() . '/' . ltrim($action->getDestination(), '\\/');
        @mkdir($folder . 'classes');
        @mkdir($folder . 'files');

        // Generate namespaces json
        $namespaces = $this->getNamespaceTree($project->getNamespace());
        file_put_contents($folder . '/namespaces.json', 'namespaces(' . json_encode($namespaces) . ');');

        // Generate packages json
        $packages = $this->getPackageTree($project->getIndexes()->get('packages')->get('\\'));
        file_put_contents($folder . '/packages.json', 'packages(' . json_encode($packages) . ');');

        // Generate per-class json
        foreach ($project->getIndexes()->get('elements') as $element) {
            if ($element instanceof ClassDescriptor) {
                $this->writeClassToDisk($folder, $element, $this->transformClass($element));
                continue;
            }
            if ($element instanceof InterfaceDescriptor) {
                $this->writeClassToDisk($folder, $element, $this->transformInterface($element));
                continue;
            }
            if ($element instanceof TraitDescriptor) {
                $this->writeClassToDisk($folder, $element, $this->transformTrait($element));
                continue;
            }
        }

        // Generate files json
        foreach ($project->getFiles() as $file) {
            $this->writeFileToDisk($folder, $file, $this->transformFile($file));
        }
    }

    /**
     * Generates an associative array containing all properties and child elements for a file.
     *
     * @param FileDescriptor $element
     *
     * @return string[]
     */
    private function transformFile(FileDescriptor $element)
    {
        $file = array(
            'name' => $element->getName(),
            'path' => $element->getPath(),
            'summary' => $element->getSummary(),
            'description' => $element->getDescription(),
            'package' => $element instanceof PackageDescriptor
                ? $element->getFullyQualifiedStructuralElementName()
                : (string)$element,
            'constants' => array(),
            'functions' => array(),
            'classes' => array(),
            'interfaces' => array(),
            'traits' => array(),
            'namespace-aliases' => array(),
            'markers' => array(),
        );

        foreach ($element->getNamespaceAliases() as $alias => $namespace) {
            $file['namespace-aliases'][$alias] = $namespace;
        }

        foreach ($element->getConstants() as $constant) {
            $file['constants'][] = $this->transformConstant($constant);
        }
        foreach ($element->getFunctions() as $function) {
            $file['functions'][] = $this->transformFunction($function);
        }
        /** @var TraitDescriptor $trait */
        foreach ($element->getTraits() as $trait) {
            $file['traits'][] = $trait->getFullyQualifiedStructuralElementName();
        }
        /** @var InterfaceDescriptor $interface */
        foreach ($element->getInterfaces() as $interface) {
            $file['interface'][] = $interface->getFullyQualifiedStructuralElementName();
        }
        /** @var ClassDescriptor $class */
        foreach ($element->getClasses() as $class) {
            $file['classes'][] = $class->getFullyQualifiedStructuralElementName();
        }

        foreach ($element->getMarkers() as $marker) {
            $file['markers'] = $marker;
        }
        foreach ($element->getAllErrors() as $error) {
            $file['errors'][] = $error;
        }

        return $file;
    }

    /**
     * Generates an associative array containing all properties and child elements for a class.
     *
     * @param ClassDescriptor $element
     *
     * @return string[]
     */
    private function transformClass(ClassDescriptor $element)
    {
        $class = array(
            'type' => 'class',
            'name' => $element->getName(),
            'line' => $element->getLine(),
            'fqsen' => $element->getFullyQualifiedStructuralElementName(),
            'final' => $element->isFinal(),
            'abstract' => $element->isAbstract(),
            'namespace' => $element->getNamespace()->getFullyQualifiedStructuralElementName(),
            'summary' => $element->getSummary(),
            'description' => $element->getDescription(),
            'extends' => $element->getParent() instanceof ClassDescriptor
                ? $element->getParent()->getFullyQualifiedStructuralElementName()
                : $element->getParent(),
            'implements' => array(),
            'package' => $element instanceof PackageDescriptor
                ? $element->getFullyQualifiedStructuralElementName()
                : (string)$element,
            'file' => $element->getFile()->getPath(),
            'uses' => array(),
            'constants' => array(),
            'methods' => array(),
            'properties' => array()
        );

        /** @var TraitDescriptor|string $trait */
        foreach ($element->getUsedTraits() as $trait) {
            $class['uses'][] = $trait instanceof TraitDescriptor
                ? $trait->getFullyQualifiedStructuralElementName()
                : $trait;
        }

        /** @var InterfaceDescriptor $interface */
        foreach ($element->getInterfaces() as $interface) {
            $interfaceFqcn = is_string($interface)
                ? $interface
                : $interface->getFullyQualifiedStructuralElementName();
            $class['implements'][] = $interfaceFqcn;
        }

        /** @var ConstantDescriptor $constant */
        foreach ($element->getConstants() as $constant) {
            $class['constants'][] = $this->transformConstant($constant);
        }

        /** @var ConstantDescriptor $constant */
        foreach ($element->getInheritedConstants() as $constant) {
            $class['constants'][] = $this->transformConstant($constant);
        }

        /** @var PropertyDescriptor $property */
        foreach ($element->getProperties() as $property) {
            $class['properties'][] = $this->transformProperty($property);
        }

        /** @var PropertyDescriptor $property */
        foreach ($element->getInheritedProperties() as $property) {
            $class['properties'][] = $this->transformProperty($property);
        }

        /** @var PropertyDescriptor $property */
        foreach ($element->getMagicProperties() as $property) {
            $class['properties'][] = $this->transformProperty($property);
        }

        /** @var MethodDescriptor $method */
        foreach ($element->getMethods() as $method) {
            $class['methods'][] = $this->transformMethod($method);
        }

        /** @var MethodDescriptor $property */
        foreach ($element->getInheritedMethods() as $method) {
            $class['methods'][] = $this->transformMethod($method);
        }

        /** @var MethodDescriptor $property */
        foreach ($element->getMagicMethods() as $method) {
            $class['methods'][] = $this->transformMethod($method);
        }

        $class['tags'] = $this->transformTags($element);

        return $class;
    }

    /**
     * Generates an associative array containing all properties and child elements for an interface.
     *
     * @param InterfaceDescriptor $element
     *
     * @return string[]
     */
    private function transformInterface(InterfaceDescriptor $element)
    {
        $interface = array(
            'type' => 'interface',
            'name' => $element->getName(),
            'line' => $element->getLine(),
            'fqsen' => $element->getFullyQualifiedStructuralElementName(),
            'namespace' => $element->getNamespace()->getFullyQualifiedStructuralElementName(),
            'summary' => $element->getSummary(),
            'description' => $element->getDescription(),
            'extends' => array(),
            'package' => $element instanceof PackageDescriptor
                ? $element->getFullyQualifiedStructuralElementName()
                : (string)$element,
            'file' => $element->getFile()->getPath(),
            'constants' => array(),
            'methods' => array(),
        );

        /** @var InterfaceDescriptor $extendedInterface */
        foreach ($element->getParent() as $extendedInterface) {
            $interfaceFqcn = is_string($extendedInterface)
                ? $extendedInterface
                : $extendedInterface->getFullyQualifiedStructuralElementName();
            $interface['extends'][] = $interfaceFqcn;
        }

        /** @var ConstantDescriptor $property */
        foreach ($element->getConstants() as $constant) {
            $interface['constants'][] = $this->transformConstant($constant);
        }

        /** @var ConstantDescriptor $constant */
        foreach ($element->getInheritedConstants() as $constant) {
            $interface['constants'][] = $this->transformConstant($constant);
        }

        /** @var MethodDescriptor $method */
        foreach ($element->getMethods() as $method) {
            $interface['methods'][] = $this->transformMethod($method);
        }

        /** @var MethodDescriptor $property */
        foreach ($element->getInheritedMethods() as $method) {
            $interface['methods'][] = $this->transformMethod($method);
        }

        $interface['tags'] = $this->transformTags($element);

        return $interface;
    }

    /**
     * Generates an associative array containing all properties and child elements for a trait.
     *
     * @param TraitDescriptor $element
     *
     * @return string[]
     */
    private function transformTrait(TraitDescriptor $element)
    {
        $trait = array(
            'type' => 'trait',
            'name' => $element->getName(),
            'line' => $element->getLine(),
            'fqsen' => $element->getFullyQualifiedStructuralElementName(),
            'namespace' => $element->getNamespace()->getFullyQualifiedStructuralElementName(),
            'summary' => $element->getSummary(),
            'description' => $element->getDescription(),
            'package' => $element instanceof PackageDescriptor
                ? $element->getFullyQualifiedStructuralElementName()
                : (string)$element,
            'file' => $element->getFile()->getPath(),
            'uses' => array(),
            'constants' => array(),
            'methods' => array(),
        );

        /** @var TraitDescriptor|string $usedTrait */
        foreach ($element->getUsedTraits() as $usedTrait) {
            $trait['uses'][] = $usedTrait instanceof TraitDescriptor
                ? $usedTrait->getFullyQualifiedStructuralElementName()
                : $usedTrait;
        }

        /** @var PropertyDescriptor $property */
        foreach ($element->getProperties() as $property) {
            $trait['properties'][] = $this->transformProperty($property);
        }

        /** @var PropertyDescriptor $property */
        foreach ($element->getInheritedProperties() as $property) {
            $trait['properties'][] = $this->transformProperty($property);
        }

        /** @var PropertyDescriptor $property */
        foreach ($element->getMagicProperties() as $property) {
            $trait['properties'][] = $this->transformProperty($property);
        }

        /** @var MethodDescriptor $method */
        foreach ($element->getMethods() as $method) {
            $trait['methods'][] = $this->transformMethod($method);
        }

        /** @var MethodDescriptor $property */
        foreach ($element->getInheritedMethods() as $method) {
            $trait['methods'][] = $this->transformMethod($method);
        }

        /** @var MethodDescriptor $property */
        foreach ($element->getMagicMethods() as $method) {
            $trait['methods'][] = $this->transformMethod($method);
        }

        $trait['tags'] = $this->transformTags($element);

        return $trait;
    }

    /**
     * Generates an associative array containing all properties for a constant.
     *
     * @param ConstantDescriptor $constant
     *
     * @return string[]
     */
    private function transformConstant(ConstantDescriptor $constant)
    {
        $result = array(
            'name' => $constant->getName(),
            'fqsen' => $constant->getValue(),
            'summary' => $constant->getSummary(),
            'description' => $constant->getDescription(),
            'type' => $this->transformTypes($constant->getTypes()),
            'line' => $constant->getLine(),
            'file' => $constant->getFile()->getPath()
        );

        $fullyQualifiedNamespaceName = $constant->getNamespace() instanceof NamespaceDescriptor
            ? $constant->getNamespace()->getFullyQualifiedStructuralElementName()
            : null;
        if ($fullyQualifiedNamespaceName) {
            $result['namespace'] = $fullyQualifiedNamespaceName;
        }

        $result['tags'] = $this->transformTags($constant);

        return $result;
    }

    /**
     * Generates an associative array containing all properties for a property.
     *
     * @param PropertyDescriptor $property
     *
     * @return string[]
     */
    private function transformProperty(PropertyDescriptor $property)
    {
        $result = array(
            'name' => $property->getName(),
            'fqsen' => $property->getFullyQualifiedStructuralElementName(),
            'summary' => $property->getSummary(),
            'description' => $property->getDescription(),
            'line' => $property->getLine(),
            'visibility' => $property->getVisibility(),
            'static' => $property->isStatic(),
            'default' => $property->getDefault(),
            'type' => $this->transformTypes($property->getTypes()),
        );

        $result['tags'] = $this->transformTags($property);

        return $result;
    }

    /**
     * Generates an associative array containing all properties for a function.
     *
     * @param FunctionDescriptor $function
     *
     * @return string[]
     */
    private function transformFunction(FunctionDescriptor $function)
    {
        $result = array(
            'name' => $function->getName(),
            'namespace' => $function->getNamespace()->getFullyQualifiedStructuralElementName(),
            'fqsen' => $function->getFullyQualifiedStructuralElementName(),
            'line' => $function->getLine(),
            'summary' => $function->getSummary(),
            'description' => $function->getDescription(),
            'file' => $function->getFile()->getPath(),
            'arguments' => array()
        );

        /** @var ArgumentDescriptor $argument */
        foreach ($function->getArguments() as $argument) {
            $result['arguments'][] = $this->transformArgument($argument);
        }

        $result['tags'] = $this->transformTags($function);

        return $result;
    }

    /**
     * Generates an associative array containing all properties for a method.
     *
     * @param MethodDescriptor $method
     *
     * @return string[]
     */
    private function transformMethod(MethodDescriptor $method)
    {
        $result = array(
            'name' => $method->getName(),
            'fqsen' => $method->getFullyQualifiedStructuralElementName(),
            'summary' => $method->getSummary(),
            'description' => $method->getDescription(),
            'line' => $method->getLine(),
            'abstract' => $method->isAbstract(),
            'final' => $method->isFinal(),
            'static' => $method->isStatic(),
            'visibility' => $method->getVisibility(),
            'arguments' => array(),
        );

        /** @var ArgumentDescriptor $argument */
        foreach ($method->getArguments() as $argument) {
            $result['arguments'][] = $this->transformArgument($argument);
        }

        $result['tags'] = $this->transformTags($method);

        return $result;
    }

    /**
     * Generates an associative array containing all properties for an argument.
     *
     * @param ArgumentDescriptor $argument
     *
     * @return string[]
     */
    private function transformArgument(ArgumentDescriptor $argument)
    {
        $argument = array(
            'name' => $argument->getName(),
            'description' => $argument->getDescription(),
            'type' => $this->transformTypes($argument->getTypes()),
            'default' => $argument->getDefault(),
            'byReference' => $argument->isByReference(),
            'variadic' => $argument->isVariadic(),
        );
        return $argument;
    }

    /**
     * Generates an associative array containing all properties for all tags of the given element.
     *
     * @param DescriptorAbstract $element
     *
     * @return string
     */
    private function transformTags(DescriptorAbstract $element)
    {
        $tags = array();
        foreach ($element->getTags() as $tagName => $tagGroup) {
            $tags[$tagName] = array();

            /** @var TagDescriptor $tag */
            foreach ($tagGroup as $tag) {
                $tags[$tagName][] = $this->transformTag($tag);
            }
        }
        return $tags;
    }

    /**
     * Generates an associative array containing all properties for a tag.
     *
     * @param TagDescriptor $tag
     *
     * @return string[]
     */
    private function transformTag(TagDescriptor $tag)
    {
        $tagArray = array(
            'name' => $tag->getName(),
            'description' => $tag->getDescription(),
        );

        // TODO: make the tests below configurable from the outside so that more could be added using plugins
        if (method_exists($tag, 'getTypes')) {
            $tagArray['type'] = $this->transformTypes($tag->getTypes());
        } elseif (method_exists($tag, 'getType')) {
            $tagArray['type'] = $this->transformTypes($tag->getType());
        }
        if (method_exists($tag, 'getVariableName')) {
            $tagArray['variable'] = $tag->getVariableName();
        }
        if (method_exists($tag, 'getReference')) {
            $tagArray['link'] = $tag->getReference();
        } elseif (method_exists($tag, 'getLink')) {
            $tagArray['link'] = $tag->getLink();
        }
        if (method_exists($tag, 'getMethodName')) {
            $tagArray['methodName'] = $tag->getMethodName();
        }

        return $tagArray;
    }

    /**
     * Generates an associative array containing all types that are detected in the given type collection.
     *
     * @param DescriptorAbstract[]|string[] $types
     *
     * @return string[]
     */
    private function transformTypes($types)
    {
        $typeStrings = array();
        foreach ($types as $type) {
            $typeStrings[] = $type instanceof DescriptorAbstract
                ? $type->getFullyQualifiedStructuralElementName()
                : (string)$type;
        }

        return $typeStrings;
    }

    /**
     * Composes a tree of namespaces with their children.
     *
     * Note that only constants, functions and child-namespaces are fully specified. Classes, interfaces and
     * traits are FQCNs that can be used to look up the right details in the classes folder. This is done on
     * purpose to reduce bandwidth,
     *
     * @param NamespaceDescriptor $namespaceDescriptor
     *
     * @return string[]
     */
    private function getNamespaceTree($namespaceDescriptor)
    {
        $namespace = array(
            'name' => $namespaceDescriptor->getName(),
            'fqnn' => $namespaceDescriptor->getFullyQualifiedStructuralElementName(),
            'namespaces' => array(),
            'constants' => array(),
            'functions' => array(),
            'classes' => array(),
            'interfaces' => array(),
            'traits' => array(),
        );

        foreach ($namespaceDescriptor->getChildren() as $subNamespace) {
            $namespace['namespaces'][] = $this->getNamespaceTree($subNamespace);
        }

        /** @var ConstantDescriptor $constant */
        foreach ($namespaceDescriptor->getConstants() as $constant) {
            $namespace['constants'][] = $this->transformConstant($constant);
        }

        /** @var FunctionDescriptor $function */
        foreach ($namespaceDescriptor->getFunctions() as $function) {
            $namespace['functions'][] = $this->transformFunction($function);
        }

        /** @var ClassDescriptor $class */
        foreach ($namespaceDescriptor->getClasses() as $class) {
            $namespace['classes'][] = $class->getFullyQualifiedStructuralElementName();
        }

        /** @var TraitDescriptor $trait */
        foreach ($namespaceDescriptor->getTraits() as $trait) {
            $namespace['traits'][] = $trait->getFullyQualifiedStructuralElementName();
        }

        /** @var InterfaceDescriptor $class */
        foreach ($namespaceDescriptor->getInterfaces() as $interface) {
            $namespace['interfaces'][] = $interface->getFullyQualifiedStructuralElementName();
        }

        return $namespace;
    }

    /**
     * Composes a tree of packages with their children.
     *
     * Note that only constants, functions and child-packages are fully specified. Classes, interfaces and
     * traits are FQCNs that can be used to look up the right details in the classes folder. This is done on
     * purpose to reduce bandwidth,
     *
     * @param PackageDescriptor $packageDescriptor
     *
     * @return string[]
     */
    private function getPackageTree($packageDescriptor)
    {
        $package = array(
            'name' => $packageDescriptor->getName(),
            'fqnn' => $packageDescriptor->getFullyQualifiedStructuralElementName(),
            'packages' => array(),
            'constants' => array(),
            'functions' => array(),
            'classes' => array(),
            'interfaces' => array(),
            'traits' => array(),
        );

        foreach ($packageDescriptor->getChildren() as $subPackage) {
            $package['packages'][] = $this->getPackageTree($subPackage);
        }

        /** @var ConstantDescriptor $constant */
        foreach ($packageDescriptor->getConstants() as $constant) {
            $package['constants'][] = $this->transformConstant($constant);
        }

        /** @var FunctionDescriptor $function */
        foreach ($packageDescriptor->getFunctions() as $function) {
            $package['functions'][] = $this->transformFunction($function);
        }

        /** @var ClassDescriptor $class */
        foreach ($packageDescriptor->getClasses() as $class) {
            $package['classes'][] = $class->getFullyQualifiedStructuralElementName();
        }

        /** @var TraitDescriptor $trait */
        foreach ($packageDescriptor->getTraits() as $trait) {
            $package['traits'][] = $trait->getFullyQualifiedStructuralElementName();
        }

        /** @var InterfaceDescriptor $class */
        foreach ($packageDescriptor->getInterfaces() as $interface) {
            $package['interfaces'][] = $interface->getFullyQualifiedStructuralElementName();
        }

        return $package;
    }

    /**
     * Renders the given class to the provided folder with the FQCN in the element as filename.
     *
     * @param string                                              $folder
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $element
     * @param string[]                                            $class
     *
     * @return void
     */
    private function writeClassToDisk($folder, $element, array $class)
    {
        file_put_contents(
            $folder . 'classes/'
            . str_replace('\\', '.', ltrim($element->getFullyQualifiedStructuralElementName(), '\\'))
            . '.json',
            'classDefinition(' . json_encode($class) . ');'
        );
    }

    /**
     * Renders the given file description to the provided folder with the path in the element as filename.
     *
     * @param string         $folder
     * @param FileDescriptor $element
     * @param string[]       $file
     *
     * @return void
     */
    private function writeFileToDisk($folder, FileDescriptor $element, array $file)
    {
        file_put_contents(
            $folder . 'files/'
            . str_replace(array('\\', '/'), '.', ltrim($element->getPath(), '/\\'))
            . '.json',
            'fileDefinition(' . json_encode($file) . ');'
        );
    }
}
