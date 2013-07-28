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

abstract class DescriptorAbstract implements Filterable
{
    /** @var string */
    protected $fqsen = '';

    /** @var string */
    protected $name = '';

    /** @var NamespaceDescriptor $namespace */
    protected $namespace;

    /** @var string $package */
    protected $package = '';

    /** @var string */
    protected $summary = '';

    /** @var string */
    protected $description = '';

    /** @var FileDescriptor */
    protected $fileDescriptor;

    /** @var int */
    protected $line = 0;

    /** @var Collection */
    protected $tags;

    /** @var Collection */
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
     * @param string $name
     *
     * @return void
     */
    public function setFullyQualifiedStructuralElementName($name)
    {
        $this->fqsen = $name;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedStructuralElementName()
    {
        return $this->fqsen;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|NamespaceDescriptor $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return NamespaceDescriptor|string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $summary
     *
     * @return void
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
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
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
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
     * @param FileDescriptor $file
     * @param int    $line
     *
     * @return void
     */
    public function setLocation(FileDescriptor $file, $line = 0)
    {
        $this->fileDescriptor = $file;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->fileDescriptor ? $this->fileDescriptor->getPath() : '';
    }

    /**
     * @return FileDescriptor
     */
    public function getFile()
    {
        return $this->fileDescriptor;
    }

    /**
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
     * @param Collection $tags
     *
     * @return void
     */
    public function setTags(Collection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $package
     */
    public function setPackage($package)
    {
        $this->package = trim($package);
    }

    /**
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
     * @param Collection $errors
     */
    public function setErrors(Collection $errors)
    {
        $this->errors = $errors;
    }

    /**
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
