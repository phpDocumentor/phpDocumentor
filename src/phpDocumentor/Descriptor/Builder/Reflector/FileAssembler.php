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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Assembles an FileDescriptor using an FileReflector and ParamDescriptors.
 */
class FileAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param FileReflector $data
     * @param ParamDescriptor[] $params
     *
     * @return FileDescriptor
     */
    public function create($data, $params = array())
    {
        $fileDescriptor = new FileDescriptor($data->getHash());
        $fileDescriptor->setName($data->getName());

        $fileDescriptor->setLocation($data->getFilename());
        $fileDescriptor->setName(basename($data->getFilename()));
        $fileDescriptor->setSource($data->getContents());
        $fileDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');
        $fileDescriptor->setIncludes(new Collection($data->getIncludes()));
        $fileDescriptor->setNamespaceAliases(new Collection($data->getNamespaceAliases()));

        $this->assembleDocBlock($data->getDocBlock(), $fileDescriptor);
        $this->extractPackageFromDocBlock($data->getDocBlock());

        $this->addMarkers($data->getMarkers(), $fileDescriptor);
        $this->addConstants($data->getConstants(), $fileDescriptor);
        $this->addFunctions($data->getFunctions(), $fileDescriptor);
        $this->addClasses($data->getClasses(), $fileDescriptor);
        $this->addInterfaces($data->getInterfaces(), $fileDescriptor);
        $this->addTraits($data->getTraits(), $fileDescriptor);

        return $fileDescriptor;
    }

    /**
     * Extracts the package from the DocBlock.
     *
     * @param DocBlock $docBlock
     *
     * @return string|null
     */
    protected function extractPackageFromDocBlock($docBlock)
    {
        $packageTaqs = $docBlock ? $docBlock->getTagsByName('package') : null;

        return $packageTaqs ? trim(reset($packageTaqs)) : null;
    }

    /**
     * Registers the child constants with the generated File Descriptor.
     *
     * @param ConstantReflector[] $constants
     * @param FileDescriptor      $fileDescriptor
     *
     * @return void
     */
    protected function addConstants($constants, $fileDescriptor)
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor) {
                $constantDescriptor->setLocation($fileDescriptor, $constant->getLineNumber());
                $constantDescriptor->setPackage($fileDescriptor->getPackage());

                $fileDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
            }
        }
    }

    /**
     * Registers the child functions with the generated File Descriptor.
     *
     * @param FunctionReflector[] $functions
     * @param FileDescriptor      $fileDescriptor
     *
     * @return void
     */
    protected function addFunctions($functions, $fileDescriptor)
    {
        foreach ($functions as $function) {
            $functionDescriptor = $this->getBuilder()->buildDescriptor($function);
            if ($functionDescriptor) {
                $functionDescriptor->setLocation($fileDescriptor, $function->getLineNumber());
                $functionDescriptor->setPackage($fileDescriptor->getPackage());

                $fileDescriptor->getFunctions()->set($functionDescriptor->getName(), $functionDescriptor);
            }
        }
    }

    /**
     * Registers the child classes with the generated File Descriptor.
     *
     * @param ClassReflector[] $classes
     * @param FileDescriptor   $fileDescriptor
     *
     * @return void
     */
    protected function addClasses($classes, $fileDescriptor)
    {
        foreach ($classes as $class) {
            $classDescriptor = $this->getBuilder()->buildDescriptor($class);
            if ($classDescriptor) {
                $classDescriptor->setLocation($fileDescriptor, $class->getLineNumber());
                $classDescriptor->setPackage($fileDescriptor->getPackage());

                $fileDescriptor->getClasses()->set($classDescriptor->getName(), $classDescriptor);
            }
        }
    }

    /**
     * Registers the child interfaces with the generated File Descriptor.
     *
     * @param InterfaceReflector[] $interfaces
     * @param FileDescriptor   $fileDescriptor
     *
     * @return void
     */
    protected function addInterfaces($interfaces, $fileDescriptor)
    {
        foreach ($interfaces as $interface) {
            $interfaceDescriptor = $this->getBuilder()->buildDescriptor($interface);
            if ($interfaceDescriptor) {
                $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLineNumber());
                $interfaceDescriptor->setPackage($fileDescriptor->getPackage());

                $fileDescriptor->getInterfaces()->set($interfaceDescriptor->getName(), $interfaceDescriptor);
            }
        }
    }

    /**
     * Registers the child traits with the generated File Descriptor.
     *
     * @param TraitReflector[] $traits
     * @param FileDescriptor   $fileDescriptor
     *
     * @return void
     */
    protected function addTraits($traits, $fileDescriptor)
    {
        foreach ($traits as $trait) {
            $traitDescriptor = $this->getBuilder()->buildDescriptor($trait);
            if ($traitDescriptor) {
                $traitDescriptor->setLocation($fileDescriptor, $trait->getLineNumber());
                $traitDescriptor->setPackage($fileDescriptor->getPackage());

                $fileDescriptor->getTraits()->set($traitDescriptor->getName(), $traitDescriptor);
            }
        }
    }

    /**
     * Registers the markers that were found in a File with the File Descriptor.
     *
     * @param string[]       $markers
     * @param FileDescriptor $fileDescriptor
     *
     * @return void
     */
    protected function addMarkers($markers, $fileDescriptor)
    {
        foreach ($markers as $marker) {
            list($type, $message, $line) = $marker;
            $fileDescriptor->getMarkers()->add(
                array(
                    'type'    => $type,
                    'message' => $message,
                    'line'    => $line,
                )
            );
        }
    }
}
