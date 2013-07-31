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

use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

class ProjectDescriptor implements Interfaces\ProjectInterface
{
    /** @var string $name */
    protected $name = '';

    /** @var NamespaceDescriptor $namespace */
    protected $namespace;

    /** @var Collection $files*/
    protected $files;

    /** @var Collection $indexes */
    protected $indexes;

    /** @var Settings $settings */
    protected $settings;

    /**
     * Initializes this descriptor.
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setSettings(new Settings());

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
     * Sets the settings used to build the documentation for this project.
     *
     * @param Settings $settings
     *
     * @return void
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns the settings used to build the documentation for this project.
     *
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Checks whether the Project supports the given visibility.
     *
     * @param integer $visibility One of the VISIBILITY_* constants of the Settings class.
     *
     * @see Settings for a list of the available VISIBILITY_* constants.
     *
     * @return boolean
     */
    public function isVisibilityAllowed($visibility)
    {
        $visibilityAllowed = $this->getSettings()
            ? $this->getSettings()->getVisibility()
            : Settings::VISIBILITY_DEFAULT;

        return (bool) ($visibilityAllowed & $visibility);
    }
}
