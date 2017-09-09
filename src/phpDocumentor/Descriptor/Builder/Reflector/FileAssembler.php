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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Trait_;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Assembles an FileDescriptor using an FileReflector and ParamDescriptors.
 */
class FileAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param File $data
     *
     * @return FileDescriptor
     */
    public function create($data)
    {
        $fileDescriptor = new FileDescriptor($data->getHash());
        $fileDescriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?: $this->getBuilder()->getDefaultPackage()
        );

        $fileDescriptor->setName($data->getName());
        $fileDescriptor->setPath($data->getPath());
        if ($this->getBuilder()->getProjectDescriptor()->getSettings()->shouldIncludeSource()) {
            $fileDescriptor->setSource($data->getSource());
        }
        $fileDescriptor->setIncludes(new Collection($data->getIncludes()));
        $fileDescriptor->setNamespaceAliases(new Collection($data->getNamespaces()));

        $this->assembleDocBlock($data->getDocBlock(), $fileDescriptor);
        $this->overridePackageTag($data, $fileDescriptor);

        //$this->addMarkers($data->getMarkers(), $fileDescriptor);
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
     * @param Constant[] $constants
     * @param FileDescriptor      $fileDescriptor
     *
     * @return void
     */
    protected function addConstants($constants, $fileDescriptor)
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor) {
                $constantDescriptor->setLocation($fileDescriptor, $constant->getLocation()->getLineNumber());
                if (count($constantDescriptor->getTags()->get('package', new Collection())) == 0) {
                    $constantDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getConstants()->set(
                    (string)$constantDescriptor->getFullyQualifiedStructuralElementName(),
                    $constantDescriptor
                );
            }
        }
    }

    /**
     * Registers the child functions with the generated File Descriptor.
     *
     * @param Function_[] $functions
     * @param FileDescriptor      $fileDescriptor
     *
     * @return void
     */
    protected function addFunctions($functions, $fileDescriptor)
    {
        foreach ($functions as $function) {
            $functionDescriptor = $this->getBuilder()->buildDescriptor($function);
            if ($functionDescriptor) {
                $functionDescriptor->setLocation($fileDescriptor, $function->getLocation()->getLineNumber());
                if (count($functionDescriptor->getTags()->get('package', new Collection())) == 0) {
                    $functionDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getFunctions()->set(
                    (string)$functionDescriptor->getFullyQualifiedStructuralElementName(),
                    $functionDescriptor
                );
            }
        }
    }

    /**
     * Registers the child classes with the generated File Descriptor.
     *
     * @param Class_[] $classes
     * @param FileDescriptor   $fileDescriptor
     *
     * @return void
     */
    protected function addClasses($classes, $fileDescriptor)
    {
        foreach ($classes as $class) {
            $classDescriptor = $this->getBuilder()->buildDescriptor($class);
            if ($classDescriptor) {
                $classDescriptor->setLocation($fileDescriptor, $class->getLocation()->getLineNumber());
                if (count($classDescriptor->getTags()->get('package', new Collection())) == 0) {
                    $classDescriptor->getTags()->set(
                        'package',
                        $fileDescriptor->getTags()->get('package', new Collection())
                    );
                }

                $fileDescriptor->getClasses()->set(
                    (string)($classDescriptor->getFullyQualifiedStructuralElementName()),
                    $classDescriptor
                );
            }
        }
    }

    /**
     * Registers the child interfaces with the generated File Descriptor.
     *
     * @param Interface_[] $interfaces
     * @param FileDescriptor   $fileDescriptor
     *
     * @return void
     */
    protected function addInterfaces($interfaces, $fileDescriptor)
    {
        foreach ($interfaces as $interface) {
            $interfaceDescriptor = $this->getBuilder()->buildDescriptor($interface);
            if ($interfaceDescriptor) {
                $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLocation()->getLineNumber());
                if (count($interfaceDescriptor->getTags()->get('package', new Collection())) == 0) {
                    $interfaceDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getInterfaces()->set(
                    (string)$interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                    $interfaceDescriptor
                );
            }
        }
    }

    /**
     * Registers the child traits with the generated File Descriptor.
     *
     * @param Trait_[] $traits
     * @param FileDescriptor   $fileDescriptor
     *
     * @return void
     */
    protected function addTraits($traits, $fileDescriptor)
    {
        foreach ($traits as $trait) {
            $traitDescriptor = $this->getBuilder()->buildDescriptor($trait);
            if ($traitDescriptor) {
                $traitDescriptor->setLocation($fileDescriptor, $trait->getLocation()->getLineNumber());
                if (count($traitDescriptor->getTags()->get('package', new Collection())) == 0) {
                    $traitDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getTraits()->set(
                    (string)$traitDescriptor->getFullyQualifiedStructuralElementName(),
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

    /**
     * @param $data
     * @param $fileDescriptor
     */
    protected function overridePackageTag($data, $fileDescriptor)
    {
        $packages = new Collection();
        $package  = $this->extractPackageFromDocBlock($data->getDocBlock());
        if (! $package) {
            $package = $this->getBuilder()->getDefaultPackage();
        }
        $tag = new TagDescriptor('package');
        $tag->setDescription($package);
        $packages->add($tag);
        $fileDescriptor->getTags()->set('package', $packages);
    }
}
