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
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
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
     *
     * @return FileDescriptor
     */
    public function create($data)
    {
        $fileDescriptor = new FileDescriptor($data->getHash());

        $fileDescriptor->setName(basename($data->getFilename()));
        $fileDescriptor->setPath($data->getFilename());
        $fileDescriptor->setSource($data->getContents());
        $fileDescriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?: $data->getDefaultPackageName()
        );
        $fileDescriptor->setIncludes(new Collection($data->getIncludes()));
        $fileDescriptor->setNamespaceAliases(new Collection($data->getNamespaceAliases()));

        $this->assembleDocBlock($data->getDocBlock(), $fileDescriptor);

        $this->addMarkers($data->getMarkers(), $fileDescriptor);
        $this->addConstants($data->getConstants(), $fileDescriptor);
        $this->addFunctions($data->getFunctions(), $fileDescriptor);
        $this->addClasses($data->getClasses(), $fileDescriptor);
        $this->addInterfaces($data->getInterfaces(), $fileDescriptor);
        $this->addTraits($data->getTraits(), $fileDescriptor);

        return $fileDescriptor;
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
                if ($constantDescriptor->getPackage() === '') {
                    $constantDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getConstants()->set(
                    $constantDescriptor->getFullyQualifiedStructuralElementName(),
                    $constantDescriptor
                );
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
                if ($functionDescriptor->getPackage() === '') {
                    $functionDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getFunctions()->set(
                    $functionDescriptor->getFullyQualifiedStructuralElementName(),
                    $functionDescriptor
                );
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
                if ($classDescriptor->getPackage() === '') {
                    $classDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getClasses()->set(
                    $classDescriptor->getFullyQualifiedStructuralElementName(),
                    $classDescriptor
                );
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
                if ($interfaceDescriptor->getPackage() === '') {
                    $interfaceDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getInterfaces()->set(
                    $interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                    $interfaceDescriptor
                );
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
                if ($traitDescriptor->getPackage() === '') {
                    $traitDescriptor->setPackage($fileDescriptor->getPackage());
                }

                $fileDescriptor->getTraits()->set(
                    $traitDescriptor->getFullyQualifiedStructuralElementName(),
                    $traitDescriptor
                );
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
