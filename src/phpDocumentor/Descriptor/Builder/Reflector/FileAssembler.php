<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Trait_;

use function count;

/**
 * Assembles an FileDescriptor using an FileReflector and ParamDescriptors.
 *
 * @extends AssemblerAbstract<FileDescriptor, File>
 */
class FileAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param File $data
     */
    public function create(object $data): FileDescriptor
    {
        $fileDescriptor = new FileDescriptor($data->getHash());
        $fileDescriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?? $this->getBuilder()->getDefaultPackage()
        );

        $fileDescriptor->setName($data->getName());
        $fileDescriptor->setPath($data->getPath());
        $fileDescriptor->setSource($data->getSource());

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
     */
    protected function addConstants(array $constants, FileDescriptor $fileDescriptor): void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant, ConstantDescriptor::class);
            if ($constantDescriptor === null) {
                continue;
            }

            $constantDescriptor->setLocation($fileDescriptor, $constant->getLocation()->getLineNumber());
            if (count($constantDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $constantDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getConstants()->set(
                (string) $constantDescriptor->getFullyQualifiedStructuralElementName(),
                $constantDescriptor
            );
        }
    }

    /**
     * Registers the child functions with the generated File Descriptor.
     *
     * @param Function_[] $functions
     */
    protected function addFunctions(array $functions, FileDescriptor $fileDescriptor): void
    {
        foreach ($functions as $function) {
            $functionDescriptor = $this->getBuilder()->buildDescriptor($function, FunctionDescriptor::class);
            if ($functionDescriptor === null) {
                continue;
            }

            $functionDescriptor->setLocation($fileDescriptor, $function->getLocation()->getLineNumber());
            if (count($functionDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $functionDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getFunctions()->set(
                (string) $functionDescriptor->getFullyQualifiedStructuralElementName(),
                $functionDescriptor
            );
        }
    }

    /**
     * Registers the child classes with the generated File Descriptor.
     *
     * @param Class_[] $classes
     */
    protected function addClasses(array $classes, FileDescriptor $fileDescriptor): void
    {
        foreach ($classes as $class) {
            $classDescriptor = $this->getBuilder()->buildDescriptor($class, ClassDescriptor::class);
            if ($classDescriptor === null) {
                continue;
            }

            $classDescriptor->setLocation($fileDescriptor, $class->getLocation()->getLineNumber());
            if (count($classDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $classDescriptor->getTags()->set(
                    'package',
                    $fileDescriptor->getTags()->fetch('package', new Collection())
                );
            }

            $fileDescriptor->getClasses()->set(
                (string) $classDescriptor->getFullyQualifiedStructuralElementName(),
                $classDescriptor
            );
        }
    }

    /**
     * Registers the child interfaces with the generated File Descriptor.
     *
     * @param Interface_[] $interfaces
     */
    protected function addInterfaces(array $interfaces, FileDescriptor $fileDescriptor): void
    {
        foreach ($interfaces as $interface) {
            $interfaceDescriptor = $this->getBuilder()->buildDescriptor($interface, InterfaceDescriptor::class);
            if ($interfaceDescriptor === null) {
                continue;
            }

            $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLocation()->getLineNumber());
            if (count($interfaceDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $interfaceDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getInterfaces()->set(
                (string) $interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                $interfaceDescriptor
            );
        }
    }

    /**
     * Registers the child traits with the generated File Descriptor.
     *
     * @param Trait_[] $traits
     */
    protected function addTraits(array $traits, FileDescriptor $fileDescriptor): void
    {
        foreach ($traits as $trait) {
            $traitDescriptor = $this->getBuilder()->buildDescriptor($trait, TraitDescriptor::class);
            if ($traitDescriptor === null) {
                continue;
            }

            $traitDescriptor->setLocation($fileDescriptor, $trait->getLocation()->getLineNumber());
            if (count($traitDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $traitDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getTraits()->set(
                (string) $traitDescriptor->getFullyQualifiedStructuralElementName(),
                $traitDescriptor
            );
        }
    }

    /**
     * Registers the markers that were found in a File with the File Descriptor.
     *
     * @param array<array<string>> $markers
     */
    protected function addMarkers(array $markers, FileDescriptor $fileDescriptor): void
    {
        foreach ($markers as $marker) {
            [$type, $message, $line] = $marker;
            $fileDescriptor->getMarkers()->add(
                [
                    'type' => $type,
                    'message' => $message,
                    'line' => $line,
                ]
            );
        }
    }

    protected function overridePackageTag(File $data, FileDescriptor $fileDescriptor): void
    {
        $packages = Collection::fromClassString(TagDescriptor::class);
        $package  = $this->extractPackageFromDocBlock($data->getDocBlock());
        if (!$package) {
            $package = $this->getBuilder()->getDefaultPackage();
        }

        $tag = new TagDescriptor('package');
        $tag->setDescription(new DescriptionDescriptor(new Description($package), []));
        $packages->add($tag);
        $fileDescriptor->getTags()->set('package', $packages);
    }
}
