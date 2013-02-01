<?php
namespace phpDocumentor\Descriptor;

class ProjectDescriptor
{
    /** @var string */
    protected $name = '';

    /** @var \ArrayObject */
    protected $namespaces;

    /** @var \ArrayObject */
    protected $files;

    /** @var \ArrayObject */
    protected $indexes;

    /**
     * Initializes this descriptor.
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setNamespaces(new \ArrayObject());
        $this->setFiles(new \ArrayObject());
        $this->setIndexes(new \ArrayObject());
    }

    /**
     * @param string $name
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
     * @param \ArrayObject $files
     */
    protected function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return \ArrayObject
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param \ArrayObject $indexes
     */
    protected function setIndexes($indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @return \ArrayObject
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param \ArrayObject $namespaces
     */
    protected function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * @return \ArrayObject
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }
}
