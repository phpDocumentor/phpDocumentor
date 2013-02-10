<?php
namespace phpDocumentor\Descriptor;

class ProjectDescriptor implements Interfaces\ProjectInterface
{
    /** @var string */
    protected $name = '';

    /** @var NamespaceDescriptor */
    protected $namespace;

    /** @var Collection */
    protected $files;

    /** @var Collection */
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
        $this->setFiles(new Collection());
        $this->setIndexes(new Collection());
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
     * @param Collection $files
     */
    protected function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param Collection $indexes
     */
    protected function setIndexes(Collection $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @return Collection
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param NamespaceDescriptor $namespace
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
