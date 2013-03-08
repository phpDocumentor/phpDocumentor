<?php
namespace phpDocumentor\Descriptor;

/**
 *
 */
abstract class DescriptorAbstract
{
    /** @var string */
    protected $fqsen = '';

    /** @var string */
    protected $name = '';

    /** @var NamespaceDescriptor $namespace */
    protected $namespace;

    /** @var string */
    protected $summary = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $path = '';

    /** @var int */
    protected $line = 0;

    /** @var Collection */
    protected $tags;

    /**
     * Initializes this descriptor.
     */
    public function __construct()
    {
        $this->setTags(new Collection());
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
     * @return NamespaceDescriptor|null
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
        return $this->description;
    }

    /**
     * @param string $file
     * @param int    $line
     *
     * @return void
     */
    public function setLocation($file, $line = 0)
    {
        $this->path = $file;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param Collection $tags
     *
     * @return void
     */
    protected function setTags(Collection $tags)
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
}
