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

class ProjectDescriptor implements Interfaces\ProjectInterface
{
    const VISIBILITY_PUBLIC    = 1;
    const VISIBILITY_PROTECTED = 2;
    const VISIBILITY_PRIVATE   = 4;
    const VISIBILITY_INTERNAL  = 8;

    /** @var integer by default ignore internal visibility but show others */
    const VISIBILITY_DEFAULT   = 7;

    /** @var string */
    protected $name = '';

    /** @var NamespaceDescriptor */
    protected $namespace;

    /** @var Collection */
    protected $files;

    /** @var Collection */
    protected $indexes;

    /** @var integer A bitflag representing which visibility modifiers are allowed to be included */
    protected $visibilityFlag = self::VISIBILITY_DEFAULT;

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
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return Collection|FileDescriptor[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param Collection $indexes
     */
    public function setIndexes(Collection $indexes)
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
    public function setNamespace($namespace)
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

    /**
     * Stores the visibilities that are allowed to be executed as a bitflag.
     *
     * @param integer $visibilityFlag A bitflag combining the VISIBILITY_* constants.
     *
     * @return void
     */
    public function setAllowedVisibility($visibilityFlag)
    {
        $this->visibilityFlag = $visibilityFlag;
    }

    /**
     * Returns the bit flag representing which visibilities are allowed.
     *
     * @see self::isVisibilityAllowed() for a convenience method to easily check against a specific visibility.
     *
     * @return integer
     */
    public function getAllowedVisibility()
    {
        return $this->visibilityFlag;
    }

    /**
     * Checks whether the Project supports the given visibility.
     *
     * @param integer $visibility One of the VISIBILITY_* constants of this class.
     *
     * @return boolean
     */
    public function isVisibilityAllowed($visibility)
    {
        return (bool)($this->getAllowedVisibility() & $visibility);
    }
}
