<?php
namespace phpDocumentor\Descriptor;

class ProjectDescriptor implements Interfaces\ProjectInterface
{
    /** @var string */
    protected $name = '';

    /** @var NamespaceDescriptor */
    protected $namespace;

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
        $namespace = new NamespaceDescriptor();
        $namespace->setName('\\');
        $namespace->setFullyQualifiedStructuralElementName('\\');
        $this->setNamespace($namespace);
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
     * @param NamespaceDescriptor $namespaces
     */
    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return NamespaceDescriptor
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
