<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Validation\Error;
use function method_exists;

/**
 * Represents a file in the project.
 */
class FileDescriptor extends DescriptorAbstract implements Interfaces\FileInterface
{
    /** @var string $hash */
    protected $hash;

    /** @var string $path */
    protected $path = '';

    /** @var string|null $source */
    protected $source = null;

    /** @var Collection<NamespaceDescriptor> $namespaceAliases */
    protected $namespaceAliases;

    /** @var Collection $includes */
    protected $includes;

    /** @var Collection<ConstantDescriptor> $constants */
    protected $constants;

    /** @var Collection<FunctionDescriptor> $functions */
    protected $functions;

    /** @var Collection<ClassDescriptor> $classes */
    protected $classes;

    /** @var Collection<InterfaceDescriptor> $interfaces */
    protected $interfaces;

    /** @var Collection<TraitDescriptor> $traits */
    protected $traits;

    /** @var Collection<array<int|string, mixed>> $markers */
    protected $markers;

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

        $this->setConstants(new Collection());
        $this->setFunctions(new Collection());
        $this->setClasses(new Collection());
        $this->setInterfaces(new Collection());
        $this->setTraits(new Collection());

        $this->setMarkers(new Collection());
    }

    /**
     * Returns the hash of the contents for this file.
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * Sets the hash of the contents for this file.
     */
    protected function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }

    /**
     * Retrieves the contents of this file.
     */
    public function getSource() : ?string
    {
        return $this->source;
    }

    /**
     * Sets the source contents for this file.
     */
    public function setSource(?string $source) : void
    {
        $this->source = $source;
    }

    /**
     * Returns the namespace aliases that have been defined in this file.
     *
     * @return Collection<NamespaceDescriptor>
     */
    public function getNamespaceAliases() : Collection
    {
        return $this->namespaceAliases;
    }

    /**
     * Sets the collection of namespace aliases for this file.
     *
     * @param Collection<NamespaceDescriptor> $namespaceAliases
     */
    public function setNamespaceAliases(Collection $namespaceAliases) : void
    {
        $this->namespaceAliases = $namespaceAliases;
    }

    /**
     * Returns a list of all includes that have been declared in this file.
     */
    public function getIncludes() : Collection
    {
        return $this->includes;
    }

    /**
     * Sets a list of all includes that have been declared in this file.
     */
    public function setIncludes(Collection $includes) : void
    {
        $this->includes = $includes;
    }

    /**
     * Returns a list of constant descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getConstants() : Collection
    {
        return $this->constants;
    }

    /**
     * Sets a list of constant descriptors contained in this file.
     *
     * @param Collection<ConstantDescriptor> $constants
     */
    public function setConstants(Collection $constants) : void
    {
        $this->constants = $constants;
    }

    /**
     * Returns a list of function descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getFunctions() : Collection
    {
        return $this->functions;
    }

    /**
     * Sets a list of function descriptors contained in this file.
     *
     * @param Collection<FunctionDescriptor> $functions
     */
    public function setFunctions(Collection $functions) : void
    {
        $this->functions = $functions;
    }

    /**
     * Returns a list of class descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getClasses() : Collection
    {
        return $this->classes;
    }

    /**
     * Sets a list of class descriptors contained in this file.
     *
     * @param Collection<ClassDescriptor> $classes
     */
    public function setClasses(Collection $classes) : void
    {
        $this->classes = $classes;
    }

    /**
     * Returns a list of interface descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getInterfaces() : Collection
    {
        return $this->interfaces;
    }

    /**
     * Sets a list of interface descriptors contained in this file.
     *
     * @param Collection<InterfaceDescriptor> $interfaces
     */
    public function setInterfaces(Collection $interfaces) : void
    {
        $this->interfaces = $interfaces;
    }

    /**
     * Returns a list of trait descriptors contained in this file.
     *
     * {@inheritDoc}
     */
    public function getTraits() : Collection
    {
        return $this->traits;
    }

    /**
     * Sets a list of trait descriptors contained in this file.
     *
     * @param Collection<TraitDescriptor> $traits
     */
    public function setTraits(Collection $traits) : void
    {
        $this->traits = $traits;
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
    public function getMarkers() : Collection
    {
        return $this->markers;
    }

    /**
     * Sets a series of markers contained in this file.
     *
     * @see getMarkers() for more information on markers.
     *
     * @param Collection<array<int|string, mixed>> $markers
     */
    public function setMarkers(Collection $markers) : void
    {
        $this->markers = $markers;
    }

    /**
     * Returns a list of all errors in this file and all its child elements.
     *
     * @return Collection<?Error>
     */
    public function getAllErrors() : Collection
    {
        $errors = $this->getErrors();

        $types = $this->getClasses()->merge($this->getInterfaces())->merge($this->getTraits());

        $elements = $this->getFunctions()->merge($this->getConstants())->merge($types);

        foreach ($elements as $element) {
            if (!$element) {
                continue;
            }

            $errors = $errors->merge($element->getErrors());
        }

        foreach ($types as $element) {
            if (!$element) {
                continue;
            }

            foreach ($element->getMethods() as $item) {
                if (!$item) {
                    continue;
                }

                $errors = $errors->merge($item->getErrors());
            }

            if (method_exists($element, 'getConstants')) {
                foreach ($element->getConstants() as $item) {
                    if (!$item) {
                        continue;
                    }

                    $errors = $errors->merge($item->getErrors());
                }
            }

            if (!method_exists($element, 'getProperties')) {
                continue;
            }

            foreach ($element->getProperties() as $item) {
                if (!$item) {
                    continue;
                }

                $errors = $errors->merge($item->getErrors());
            }
        }

        return $errors;
    }

    /**
     * Sets the file path for this file relative to the project's root.
     */
    public function setPath(string $path) : void
    {
        $this->path = $path;
    }

    /**
     * Returns the file path relative to the project's root.
     */
    public function getPath() : string
    {
        return $this->path;
    }

    public function __toString() : string
    {
        return $this->getPath();
    }
}
