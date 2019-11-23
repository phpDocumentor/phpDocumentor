<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Trait_;

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
     */
    protected function addConstants(array $constants, FileDescriptor $fileDescriptor): void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor) {
                $constantDescriptor->setLocation($fileDescriptor, $constant->getLocation()->getLineNumber());
                if (count($constantDescriptor->getTags()->get('package', new Collection())) === 0) {
                    $constantDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getConstants()->set(
                    (string) $constantDescriptor->getFullyQualifiedStructuralElementName(),
                    $constantDescriptor
                );
            }
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
            $functionDescriptor = $this->getBuilder()->buildDescriptor($function);
            if ($functionDescriptor) {
                $functionDescriptor->setLocation($fileDescriptor, $function->getLocation()->getLineNumber());
                if (count($functionDescriptor->getTags()->get('package', new Collection())) === 0) {
                    $functionDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getFunctions()->set(
                    (string) $functionDescriptor->getFullyQualifiedStructuralElementName(),
                    $functionDescriptor
                );
            }
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
            $classDescriptor = $this->getBuilder()->buildDescriptor($class);
            if ($classDescriptor) {
                $classDescriptor->setLocation($fileDescriptor, $class->getLocation()->getLineNumber());
                if (count($classDescriptor->getTags()->get('package', new Collection())) === 0) {
                    $classDescriptor->getTags()->set(
                        'package',
                        $fileDescriptor->getTags()->get('package', new Collection())
                    );
                }

                $fileDescriptor->getClasses()->set(
                    (string) ($classDescriptor->getFullyQualifiedStructuralElementName()),
                    $classDescriptor
                );
            }
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
            $interfaceDescriptor = $this->getBuilder()->buildDescriptor($interface);
            if ($interfaceDescriptor) {
                $interfaceDescriptor->setLocation($fileDescriptor, $interface->getLocation()->getLineNumber());
                if (count($interfaceDescriptor->getTags()->get('package', new Collection())) === 0) {
                    $interfaceDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getInterfaces()->set(
                    (string) $interfaceDescriptor->getFullyQualifiedStructuralElementName(),
                    $interfaceDescriptor
                );
            }
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
            $traitDescriptor = $this->getBuilder()->buildDescriptor($trait);
            if ($traitDescriptor) {
                $traitDescriptor->setLocation($fileDescriptor, $trait->getLocation()->getLineNumber());
                if (count($traitDescriptor->getTags()->get('package', new Collection())) === 0) {
                    $traitDescriptor->getTags()
                        ->set('package', $fileDescriptor->getTags()->get('package', new Collection()));
                }

                $fileDescriptor->getTraits()->set(
                    (string) $traitDescriptor->getFullyQualifiedStructuralElementName(),
                    $traitDescriptor
                );
            }
        }
    }

    /**
     * Registers the markers that were found in a File with the File Descriptor.
     *
     * @param string[]       $markers
     */
    protected function addMarkers(array $markers, FileDescriptor $fileDescriptor): void
    {
        foreach ($markers as $marker) {
            list($type, $message, $line) = $marker;
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
        $packages = new Collection();
        $package = $this->extractPackageFromDocBlock($data->getDocBlock());
        if (! $package) {
            $package = $this->getBuilder()->getDefaultPackage();
        }

        $tag = new TagDescriptor('package');
        $tag->setDescription($package);
        $packages->add($tag);
        $fileDescriptor->getTags()->set('package', $packages);
    }
}
