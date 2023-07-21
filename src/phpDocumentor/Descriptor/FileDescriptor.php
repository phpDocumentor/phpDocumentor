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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\TracksErrors;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\Validation\Error;
use phpDocumentor\Reflection\Fqsen;
use Stringable;

/**
 * Represents a file in the project.
 *
 * This class contains all structural elements of the file it represents. In most modern projects a
 * file will contain a single element like a Class, Interface or Trait, sometimes multiple functions.
 * Depending on the config settings of the parsed project it might include all source code from the file in the project.
 *
 * @api
 * @package phpDocumentor\AST
 */
class FileDescriptor extends DescriptorAbstract implements Interfaces\FileInterface, Stringable
{
    protected string $hash = '';
    protected string $path = '';
    protected string|null $source = null;

    /** @var Collection<NamespaceInterface|Fqsen> $namespaceAliases */
    protected Collection $namespaceAliases;

    /** @var Collection<string> $includes */
    protected Collection $includes;

    /** @var Collection<ConstantInterface> $constants */
    protected Collection $constants;

    /** @var Collection<FunctionInterface> $functions */
    protected Collection $functions;

    /** @var Collection<ClassInterface> $classes */
    protected Collection $classes;

    /** @var Collection<InterfaceInterface> $interfaces */
    protected Collection $interfaces;

    /** @var Collection<TraitInterface> $traits */
    protected Collection $traits;

    /** @var Collection<array<int|string, mixed>> $markers */
    protected Collection $markers;

    /** @var Collection<EnumInterface> */
    private Collection $enums;

    /**
     * Initializes a new file descriptor with the given hash of its contents.
     *
     * @param string $hash An MD5 hash of the contents if this file.
     */
    public function __construct(string $hash)
    {
        parent::__construct();

        $this->setHash($hash);
        $this->setNamespaceAliases(new Collection());
        $this->setIncludes(new Collection());

        $this->setConstants(Collection::fromInterfaceString(ConstantInterface::class));
        $this->setFunctions(Collection::fromInterfaceString(FunctionInterface::class));
        $this->setClasses(Collection::fromInterfaceString(ClassInterface::class));
        $this->setInterfaces(Collection::fromInterfaceString(InterfaceInterface::class));
        $this->setTraits(Collection::fromInterfaceString(TraitInterface::class));
        $this->setEnums(Collection::fromInterfaceString(EnumInterface::class));
        $this->setMarkers(new Collection());
    }

    /**
     * Returns the hash of the contents for this file.
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Sets the hash of the contents for this file.
     */
    protected function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * Retrieves the contents of this file.
     *
     * When source is included in parsing process this property will contain the raw file contents.
     */
    public function getSource(): string|null
    {
        return $this->source;
    }

    /**
     * Sets the source contents for this file.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setSource(string|null $source): void
    {
        $this->source = $source;
    }

    /**
     * Returns the namespace aliases that have been defined in this file.
     *
     * A namespace alias can either be a full descriptor of the namespace or just a {@see Fqsen}
     * when the namespace was not part of the processed code. When it is a {@see NamespaceDescriptor} it
     * will contain all structural elements in the namespace not just the once in this particlar file.
     *
     * @return Collection<NamespaceInterface|Fqsen>
     */
    public function getNamespaceAliases(): Collection
    {
        return $this->namespaceAliases;
    }

    /**
     * Sets the collection of namespace aliases for this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<NamespaceInterface|Fqsen> $namespaceAliases
     */
    public function setNamespaceAliases(Collection $namespaceAliases): void
    {
        $this->namespaceAliases = $namespaceAliases;
    }

    /**
     * Returns a list of all includes that have been declared in this file.
     *
     * @return Collection<string>
     */
    public function getIncludes(): Collection
    {
        return $this->includes;
    }

    /**
     * Sets a list of all includes that have been declared in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<string> $includes
     */
    public function setIncludes(Collection $includes): void
    {
        $this->includes = $includes;
    }

    /**
     * Returns a list of constant descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getConstants(): Collection
    {
        return $this->constants;
    }

    /**
     * Sets a list of constant descriptors contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<ConstantInterface> $constants
     */
    public function setConstants(Collection $constants): void
    {
        $this->constants = $constants;
    }

    /**
     * Returns a list of function descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getFunctions(): Collection
    {
        return $this->functions;
    }

    /**
     * Sets a list of function descriptors contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<FunctionInterface> $functions
     */
    public function setFunctions(Collection $functions): void
    {
        $this->functions = $functions;
    }

    /**
     * Returns a list of class descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    /**
     * Sets a list of class descriptors contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<ClassInterface> $classes
     */
    public function setClasses(Collection $classes): void
    {
        $this->classes = $classes;
    }

    /**
     * Returns a list of interface descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getInterfaces(): Collection
    {
        return $this->interfaces;
    }

    /**
     * Sets a list of interface descriptors contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<InterfaceInterface> $interfaces
     */
    public function setInterfaces(Collection $interfaces): void
    {
        $this->interfaces = $interfaces;
    }

    /**
     * Returns a list of trait descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getTraits(): Collection
    {
        return $this->traits;
    }

    /**
     * Sets a list of trait descriptors contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<TraitInterface> $traits
     */
    public function setTraits(Collection $traits): void
    {
        $this->traits = $traits;
    }

    /** @return Collection<EnumInterface> */
    public function getEnums(): Collection
    {
        return $this->enums;
    }

    /**
     * Sets a list of enum descriptors contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<EnumInterface> $enums
     */
    public function setEnums(Collection $enums): void
    {
        $this->enums = $enums;
    }

    /**
     * Returns a series of markers contained in this file.
     *
     * A marker is a special inline comment that starts with a keyword and is followed by a single line description.
     *
     * Example:
     * ```
     * // TODO: This is an item that needs to be done.
     * ```
     *
     * @return Collection<array<int|string, mixed>>
     */
    public function getMarkers(): Collection
    {
        return $this->markers;
    }

    /**
     * Sets a series of markers contained in this file.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @see getMarkers() for more information on markers.
     *
     * @param Collection<array<int|string, mixed>> $markers
     */
    public function setMarkers(Collection $markers): void
    {
        $this->markers = $markers;
    }

    /**
     * Returns a list of all errors in this file and all its child elements.
     *
     * All errors from structural elements in the file are collected to the deepest level.
     *
     * @return Collection<Error>
     */
    public function getAllErrors(): Collection
    {
        $errors = $this->getErrors();

        $structuralElements = Collection::fromInterfaceString(TracksErrors::class);

        /** @var Collection<TracksErrors> $classes */
        $classes = $this->getClasses();
        /** @var Collection<TracksErrors> $interfaces */
        $interfaces = $this->getInterfaces();
        /** @var Collection<TracksErrors> $traits */
        $traits = $this->getTraits();
        /** @var Collection<TracksErrors> $enums */
        $enums = $this->getEnums();

        $structuralElements = $structuralElements
            ->merge($classes)
            ->merge($interfaces)
            ->merge($traits)
            ->merge($enums);

        /** @var Collection<TracksErrors> $functions */
        $functions = $this->getFunctions();
        /** @var Collection<TracksErrors> $constants */
        $constants = $this->getConstants();

        $elements = Collection::fromInterfaceString(TracksErrors::class);
        $elements = $elements
            ->merge($functions)
            ->merge($constants)
            ->merge($structuralElements);

        foreach ($elements as $element) {
            $errors = $errors->merge($element->getErrors());
        }

        foreach ($structuralElements as $element) {
            if (
                $element instanceof ClassInterface ||
                $element instanceof InterfaceInterface ||
                $element instanceof TraitInterface ||
                $element instanceof EnumInterface
            ) {
                foreach ($element->getMethods() as $item) {
                    $errors = $errors->merge($item->getErrors());
                }
            }

            if (
                $element instanceof ClassInterface ||
                $element instanceof InterfaceInterface
            ) {
                foreach ($element->getConstants() as $item) {
                    $errors = $errors->merge($item->getErrors());
                }
            }

            if (
                ! $element instanceof ClassInterface &&
                ! $element instanceof TraitInterface
            ) {
                continue;
            }

            foreach ($element->getProperties() as $item) {
                $errors = $errors->merge($item->getErrors());
            }
        }

        return $errors;
    }

    /**
     * Sets the file path for this file relative to the project's root.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Returns the relative file path.
     *
     * The path is a relative to the source file based on the dsn of the config.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function __toString(): string
    {
        return $this->getPath();
    }
}
