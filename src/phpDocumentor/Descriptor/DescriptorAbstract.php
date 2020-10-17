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

use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Validation\Error;
use phpDocumentor\Reflection\Fqsen;
use function lcfirst;
use function strpos;
use function strtolower;
use function substr;
use function trim;

/**
 * Base class for descriptors containing the most used options.
 */
abstract class DescriptorAbstract implements Filterable
{
    /** @var Fqsen Fully Qualified Structural Element Name; the FQCN including method, property of constant name */
    protected $fqsen;

    /** @var string $name The local name for this element */
    protected $name = '';

    /** @var NamespaceDescriptor|string $namespace The namespace for this element */
    protected $namespace = '';

    /** @var PackageDescriptor|string $package The package with which this element is associated */
    protected $package;

    /** @var string $summary A summary describing the function of this element in short. */
    protected $summary = '';

    /** @var DocBlock\DescriptionDescriptor|null $description A more extensive description of this element. */
    protected $description;

    /** @var FileDescriptor|null $fileDescriptor The file to which this element belongs; if applicable */
    protected $fileDescriptor;

    /** @var int $line The line number on which this element occurs. */
    protected $line = 0;

    /** @var Collection<Collection<TagDescriptor>> $tags The tags associated with this element. */
    protected $tags;

    /** @var Collection<Validation\Error> $errors A list of errors found while building this element. */
    protected $errors;

    /** @var DescriptorAbstract|null the element from which to inherit information in this element */
    protected $inheritedElement = null;

    /**
     * Initializes this descriptor.
     */
    public function __construct()
    {
        $this->setTags(new Collection());
        $this->setErrors(new Collection());
    }

    /**
     * Sets the Fully Qualified Structural Element Name (FQSEN) for this element.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setFullyQualifiedStructuralElementName(Fqsen $name) : void
    {
        $this->fqsen = $name;
    }

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function getFullyQualifiedStructuralElementName() : ?Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Sets the local name for this element.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * Returns the local name for this element.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Sets the namespace (name) for this element.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param NamespaceDescriptor|string $namespace
     */
    public function setNamespace($namespace) : void
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the namespace for this element (defaults to global "\")
     *
     * @return NamespaceDescriptor|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the summary describing this element in short.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setSummary(string $summary) : void
    {
        $this->summary = $summary;
    }

    /**
     * Returns the summary which describes this element.
     *
     * This method will automatically attempt to inherit the parent's summary if this one has none.
     */
    public function getSummary() : string
    {
        if ($this->summary && strtolower(trim($this->summary)) !== '{@inheritdoc}') {
            return $this->summary;
        }

        $parent = $this->getInheritedElement();
        if ($parent instanceof self) {
            return $parent->getSummary();
        }

        return $this->summary;
    }

    /**
     * Sets a description for this element.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setDescription(?DocBlock\DescriptionDescriptor $description) : void
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     */
    public function getDescription() : ?DocBlock\DescriptionDescriptor
    {
        if ($this->description !== null) {
            return $this->description;
        }

        $parentElement = $this->getInheritedElement();
        if ($parentElement instanceof self) {
            return $parentElement->getDescription();
        }

        return null;
    }

    /**
     * Sets the file and linenumber where this element is at.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setLocation(FileDescriptor $file, int $line = 0) : void
    {
        $this->setFile($file);
        $this->line = $line;
    }

    /**
     * Returns the path to the file containing this element relative to the project's root.
     */
    public function getPath() : string
    {
        return $this->fileDescriptor ? $this->fileDescriptor->getPath() : '';
    }

    /**
     * Returns the file in which this element resides or null in case the element is not bound to a file..
     */
    public function getFile() : ?FileDescriptor
    {
        return $this->fileDescriptor;
    }

    /**
     * Sets the file to which this element is associated.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setFile(FileDescriptor $file) : void
    {
        $this->fileDescriptor = $file;
    }

    /**
     * Returns the line number where the definition for this element can be found.
     */
    public function getLine() : int
    {
        return $this->line;
    }

    /**
     * Sets the line number for this element's location in the source file.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setLine(int $lineNumber) : void
    {
        $this->line = $lineNumber;
    }

    /**
     * Sets the tags associated with this element.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<Collection<TagDescriptor>> $tags
     */
    public function setTags(Collection $tags) : void
    {
        $this->tags = $tags;
    }

    /**
     * Returns the tags associated with this element.
     *
     * @return Collection<Collection<TagDescriptor>>
     */
    public function getTags() : Collection
    {
        return $this->tags;
    }

    /**
     * Sets the name of the package to which this element belongs.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param PackageDescriptor|string $package
     */
    public function setPackage($package) : void
    {
        $this->package = $package;
    }

    /**
     * Returns the package name for this element.
     */
    public function getPackage() : ?PackageDescriptor
    {
        $inheritedElement = $this->getInheritedElement();
        if ($this->package instanceof PackageDescriptor
            && !($this->package->getName() === '\\' && $inheritedElement)) {
            return $this->package;
        }

        if ($inheritedElement instanceof self) {
            return $inheritedElement->getPackage();
        }

        return null;
    }

    /**
     * @return Collection<AuthorDescriptor>
     */
    public function getAuthor() : Collection
    {
        /** @var Collection<AuthorDescriptor> $author */
        $author = $this->getTags()->fetch('author', new Collection());
        if ($author->count() !== 0) {
            return $author;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getAuthor();
        }

        return new Collection();
    }

    /**
     * Returns the versions for this element.
     *
     * @return Collection<VersionDescriptor>
     */
    public function getVersion() : Collection
    {
        /** @var Collection<VersionDescriptor> $version */
        $version = $this->getTags()->fetch('version', new Collection());
        if ($version->count() !== 0) {
            return $version;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getVersion();
        }

        return new Collection();
    }

    /**
     * Returns the copyrights for this element.
     *
     * @return Collection<TagDescriptor>
     */
    public function getCopyright() : Collection
    {
        /** @var Collection<TagDescriptor> $copyright */
        $copyright = $this->getTags()->fetch('copyright', new Collection());
        if ($copyright->count() !== 0) {
            return $copyright;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getCopyright();
        }

        return new Collection();
    }

    /**
     * Checks whether this element is deprecated.
     */
    public function isDeprecated() : bool
    {
        return isset($this->tags['deprecated']);
    }

    /**
     * Sets a list of all errors associated with this element.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<Error> $errors
     */
    public function setErrors(Collection $errors) : void
    {
        $this->errors = $errors;
    }

    /**
     * Returns all errors that occur in this element.
     *
     * @return Collection<Error>
     */
    public function getErrors() : Collection
    {
        $errors = $this->errors;
        foreach ($this->tags as $tags) {
            foreach ($tags as $tag) {
                $errors = $errors->merge($tag->getErrors());
            }
        }

        return $errors;
    }

    /**
     * Dynamically constructs a set of getters to retrieve tag (collections) with.
     *
     * Important: __call() is not a fast method of access; it is preferred to directly use the getTags() collection.
     * This interface is provided to allow for uniform and easy access to certain tags.
     *
     * @param array<mixed> $arguments
     *
     * @return Collection<TagDescriptor>|null
     */
    public function __call(string $name, array $arguments)
    {
        if (strpos($name, 'get') !== 0) {
            return null;
        }

        $tagName = substr($name, 3);
        $tagName = lcfirst($tagName);

        return $this->getTags()->fetch($tagName, new Collection());
    }

    /**
     * Represents this object by its unique identifier, the Fully Qualified Structural Element Name.
     */
    public function __toString() : string
    {
        return (string) $this->getFullyQualifiedStructuralElementName();
    }

    /**
     * @return DescriptorAbstract|string|Fqsen|null
     */
    public function getInheritedElement()
    {
        return $this->inheritedElement;
    }
}
