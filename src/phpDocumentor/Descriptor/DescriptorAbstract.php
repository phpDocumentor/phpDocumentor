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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\Interfaces\ChildInterface;

/**
 * Base class for descriptors containing the most used options.
 */
abstract class DescriptorAbstract implements Filterable
{
    /**
     * @var string $fqsen Fully Qualified Structural Element Name; the FQCN including method, property of constant name
     */
    protected $fqsen = '';

    /** @var string $name The local name for this element */
    protected $name = '';

    /** @var NamespaceDescriptor $namespace The namespace for this element */
    protected $namespace;

    /** @var string $package The package with which this element is associated */
    protected $package = '';

    /** @var string $summary A summary describing the function of this element in short. */
    protected $summary = '';

    /** @var string $description A more extensive description of this element. */
    protected $description = '';

    /** @var FileDescriptor|null $file The file to which this element belongs; if applicable */
    protected $fileDescriptor;

    /** @var int $line The line number on which this element occurs. */
    protected $line = 0;

    /** @var Collection $tags The tags associated with this element. */
    protected $tags;

    /** @var Collection $errors A list of errors found while building this element. */
    protected $errors;

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
     * @param string $name
     *
     * @return void
     */
    public function setFullyQualifiedStructuralElementName($name)
    {
        $this->fqsen = $name;
    }

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     *
     * @return string
     */
    public function getFullyQualifiedStructuralElementName()
    {
        return $this->fqsen;
    }

    /**
     * Sets the local name for this element.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the local name for this element.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the namespace (name) for this element.
     *
     * @param NamespaceDescriptor|string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the namespace for this element or null if none is attached.
     *
     * @return NamespaceDescriptor|string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the summary describing this element in short.
     *
     * @param string $summary
     *
     * @return void
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Returns the summary which describes this element.
     *
     * This method will automatically attempt to inherit the parent's summary if this one has none.
     *
     * @return string
     */
    public function getSummary()
    {
        // if the summary is not set, inherit it from the parent
        if ((!$this->summary || strtolower(trim($this->summary)) == '{@inheritdoc}')
            && ($this instanceof ChildInterface)
            && ($this->getParent() instanceof self)
        ) {
            return $this->getParent()->getSummary();
        }

        return $this->summary;
    }

    /**
     * Sets a description for this element.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     *
     * @return string
     */
    public function getDescription()
    {
        // if the description is not set, inherit it from the parent
        if (!$this->description && ($this instanceof ChildInterface) && ($this->getParent() instanceof self)) {
            return $this->getParent()->getDescription();
        }

        return $this->description;
    }

    /**
     * Sets the file and linenumber where this element is at.
     *
     * @param FileDescriptor $file
     * @param int            $line
     *
     * @return void
     */
    public function setLocation(FileDescriptor $file, $line = 0)
    {
        $this->setFile($file);
        $this->line = $line;
    }

    /**
     * Returns the path to the file containing this element relative to the project's root.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->fileDescriptor ? $this->fileDescriptor->getPath() : '';
    }

    /**
     * Returns the file in which this element resides or null in case the element is not bound to a file..
     *
     * @return FileDescriptor|null
     */
    public function getFile()
    {
        return $this->fileDescriptor;
    }

    /**
     * Sets the file to which this element is associated.
     *
     * @param FileDescriptor $file
     *
     * @return false
     */
    public function setFile(FileDescriptor $file)
    {
        $this->fileDescriptor = $file;
    }

    /**
     * Returns the line number where the definition for this element can be found.
     *
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Sets the line number for this element's location in the source file.
     *
     * @param integer $lineNumber
     *
     * @return void
     */
    public function setLine($lineNumber)
    {
        $this->line = $lineNumber;
    }

    /**
     * Sets the tags associated with this element.
     *
     * @param Collection $tags
     *
     * @return void
     */
    public function setTags(Collection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Returns the tags associated with this element.
     *
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the name of the package to which this element belongs.
     *
     * @param string $package
     *
     * @return void
     */
    public function setPackage($package)
    {
        $this->package = trim($package);
    }

    /**
     * Returns the package to which this element belongs.
     *
     * This method will automatically attempt to inherit the parent's package if this one has none.
     *
     * @return string
     */
    public function getPackage()
    {
        // if the package is not set, inherit it from the parent
        if (!$this->package && ($this instanceof ChildInterface) && ($this->getParent() instanceof self)) {
            return $this->getParent()->getPackage();
        }

        return $this->package;
    }

    /**
     * Returns the subpackage for this element.
     *
     * This method will automatically attempt to inherit the parent's subpackage if this one has none.
     *
     * @return Collection
     */
    public function getSubPackage()
    {
        /** @var Collection $subpackage */
        $subpackage = $this->getTags()->get('subpackage', new Collection());

        // if the subpackage is not set, inherit it from the parent
        if ($subpackage->count() == 0
            && ($this instanceof ChildInterface)
            && ($this->getParent() instanceof self)
            && ($this->getParent()->getPackage() == $this->getPackage())
        ) {
            return $this->getParent()->getSubPackage();
        }

        $subpackageDescriptor = current(current($subpackage));

        return $subpackageDescriptor ? $subpackageDescriptor->getDescription() : '';
    }

    /**
     * Returns the authors for this element.
     *
     * @return Collection
     */
    public function getAuthor()
    {
        /** @var Collection $author */
        $author = $this->getTags()->get('author', new Collection());

        // if the author is not set, inherit it from the parent
        if ($author->count() == 0 && ($this instanceof ChildInterface) && ($this->getParent() instanceof self)) {
            return $this->getParent()->getAuthor();
        }

        return $author;
    }

    /**
     * Returns the versions for this element.
     *
     * @return Collection
     */
    public function getVersion()
    {
        /** @var Collection $version */
        $version = $this->getTags()->get('version', new Collection());

        // if the version is not set, inherit it from the parent
        if ($version->count() == 0 && ($this instanceof ChildInterface) && ($this->getParent() instanceof self)) {
            return $this->getParent()->getVersion();
        }

        return $version;
    }

    /**
     * Returns the copyrights for this element.
     *
     * @return Collection
     */
    public function getCopyright()
    {
        /** @var Collection $copyright */
        $copyright = $this->getTags()->get('copyright', new Collection());

        // if the copyright is not set, inherit it from the parent
        if ($copyright->count() == 0 && ($this instanceof ChildInterface) && ($this->getParent() instanceof self)) {
            return $this->getParent()->getCopyright();
        }

        return $copyright;
    }

    /**
     * Checks whether this element is deprecated.
     *
     * @return boolean
     */
    public function isDeprecated()
    {
        return isset($this->tags['deprecated']);
    }

    /**
     * Sets a list of all errors associated with this element.
     *
     * @param Collection $errors
     */
    public function setErrors(Collection $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Returns all errors that occur in this element.
     *
     * @return Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Dynamically constructs a set of getters to retrieve tag (collections) with.
     *
     * Important: __call() is not a fast method of access; it is preferred to directly use the getTags() collection.
     * This interface is provided to allow for uniform and easy access to certain tags.
     *
     * @param string  $name
     * @param mixed[] $arguments
     *
     * @return Collection|null
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) !== 'get') {
            return null;
        }

        $tagName = substr($name, 3);
        $tagName[0] = strtolower($tagName[0]); // lowercase the first letter

        return $this->getTags()->get($tagName, new Collection());
    }

    /**
     * Represents this object by its unique identifier, the Fully Qualified Structural Element Name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFullyQualifiedStructuralElementName();
    }
}
