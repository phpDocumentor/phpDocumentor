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
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Trait_;

use function count;

/**
 * Assembles an FileDescriptor using an FileReflector and ParamDescriptors.
 *
 * @extends AssemblerAbstract<FileInterface, File>
 */
class FileAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param File $data
     */
    public function buildDescriptor(object $data): FileInterface
    {
        $fileDescriptor = new FileDescriptor($data->getHash());
        $fileDescriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?? $this->getBuilder()->getDefaultPackageName(),
        );

        $fileDescriptor->setName($data->getName());
        $fileDescriptor->setPath($data->getPath());
        $fileDescriptor->setSource($data->getSource());

        $fileDescriptor->setIncludes(new Collection($data->getIncludes()));

        /** @var Collection<NamespaceInterface|Fqsen> $namespaceAliases */
        $namespaceAliases = new Collection($data->getNamespaces());
        $fileDescriptor->setNamespaceAliases($namespaceAliases);

        $this->assembleDocBlock($data->getDocBlock(), $fileDescriptor);
        $this->overridePackageTag($data, $fileDescriptor);

        //$this->addMarkers($data->getMarkers(), $fileDescriptor);
        $this->addConstants($data->getConstants(), $fileDescriptor);
        $this->addFunctions($data->getFunctions(), $fileDescriptor);
        $this->addClasses($data->getClasses(), $fileDescriptor);
        $this->addEnums($data->getEnums(), $fileDescriptor);
        $this->addInterfaces($data->getInterfaces(), $fileDescriptor);
        $this->addTraits($data->getTraits(), $fileDescriptor);

        return $fileDescriptor;
    }

    /**
     * Registers the child constants with the generated File Descriptor.
     *
     * @param Constant[] $constants
     */
    protected function addConstants(array $constants, FileInterface $fileDescriptor): void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant, ConstantDescriptor::class);
            if ($constantDescriptor === null) {
                continue;
            }

            $constantDescriptor->setLocation($fileDescriptor, $constant->getLocation());
            if (count($constantDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $constantDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getConstants()->set(
                (string) $constantDescriptor->getFullyQualifiedStructuralElementName(),
                $constantDescriptor,
            );
        }
    }

    /**
     * Registers the child functions with the generated File Descriptor.
     *
     * @param Function_[] $functions
     */
    protected function addFunctions(array $functions, FileInterface $fileDescriptor): void
    {
        foreach ($functions as $function) {
            $functionDescriptor = $this->getBuilder()->buildDescriptor($function, FunctionDescriptor::class);
            if ($functionDescriptor === null) {
                continue;
            }

            $functionDescriptor->setLocation($fileDescriptor, $function->getLocation());
            if (count($functionDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $functionDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getFunctions()->set(
                (string) $functionDescriptor->getFullyQualifiedStructuralElementName(),
                $functionDescriptor,
            );
        }
    }

    /**
     * Registers the child classes with the generated File Descriptor.
     *
     * @param Class_[] $classes
     */
    protected function addClasses(array $classes, FileInterface $fileDescriptor): void
    {
        foreach ($classes as $class) {
            $classDescriptor = $this->getBuilder()->buildDescriptor($class, ClassDescriptor::class);
            if ($classDescriptor === null) {
                continue;
            }

            $classDescriptor->setLocation($fileDescriptor, $class->getLocation());
            if (count($classDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $classDescriptor->getTags()->set(
                    'package',
                    $fileDescriptor->getTags()->fetch('package', new Collection()),
                );
            }

            $fileDescriptor->getClasses()->set(
                (string) $classDescriptor->getFullyQualifiedStructuralElementName(),
                $classDescriptor,
            );
        }
    }

    /**
     * Registers the child classes with the generated File Descriptor.
     *
     * @param Enum_[] $enums
     */
    private function addEnums(array $enums, FileInterface $fileDescriptor): void
    {
        foreach ($enums as $enum) {
            $enumDescriptor = $this->getBuilder()->buildDescriptor($enum, EnumDescriptor::class);
            if ($enumDescriptor === null) {
                continue;
            }

            $enumDescriptor->setLocation($fileDescriptor, $enum->getLocation());
            if (count($enumDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $enumDescriptor->getTags()->set(
                    'package',
                    $fileDescriptor->getTags()->fetch('package', new Collection()),
                );
            }

            $fileDescriptor->getEnums()->set(
                (string) $enumDescriptor->getFullyQualifiedStructuralElementName(),
                $enumDescriptor,
            );
        }
    }

    /**
     * Registers the child interfaces with the generated File Descriptor.
     *
     * @param Interface_[] $interfaces
     */
    protected function addInterfaces(array $interfaces, FileInterface $fileDescriptor): void
    {
        foreach ($interfaces as $interface) {
            $interfaceDescriptor = $this->getBuilder()->buildDescriptor($interface, InterfaceDescriptor::class);
            if ($interfaceDescriptor === null) {
                continue;
            }

            $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLocation());
            if (count($interfaceDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $interfaceDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getInterfaces()->set(
                (string) $interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                $interfaceDescriptor,
            );
        }
    }

    /**
     * Registers the child traits with the generated File Descriptor.
     *
     * @param Trait_[] $traits
     */
    protected function addTraits(array $traits, FileInterface $fileDescriptor): void
    {
        foreach ($traits as $trait) {
            $traitDescriptor = $this->getBuilder()->buildDescriptor($trait, TraitDescriptor::class);
            if ($traitDescriptor === null) {
                continue;
            }

            $traitDescriptor->setLocation($fileDescriptor, $trait->getLocation());
            if (count($traitDescriptor->getTags()->fetch('package', new Collection())) === 0) {
                $traitDescriptor->getTags()
                    ->set('package', $fileDescriptor->getTags()->fetch('package', new Collection()));
            }

            $fileDescriptor->getTraits()->set(
                (string) $traitDescriptor->getFullyQualifiedStructuralElementName(),
                $traitDescriptor,
            );
        }
    }

    protected function overridePackageTag(File $data, FileInterface $fileDescriptor): void
    {
        $packages = Collection::fromClassString(TagDescriptor::class);
        $package  = $this->extractPackageFromDocBlock($data->getDocBlock());
        if (! $package) {
            $package = $this->getBuilder()->getDefaultPackageName();
        }

        $tag = new TagDescriptor('package');
        $tag->setDescription(new DescriptionDescriptor(new Description($package), []));
        $packages->add($tag);
        $fileDescriptor->getTags()->set('package', $packages);
    }
}
