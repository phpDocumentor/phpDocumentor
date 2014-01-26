<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Represents the entire project with its files, namespaces and indexes.
 */
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

    /** @var Collection $partials */
    protected $partials;

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

        $this->setPartials(new Collection());
    }

    /**
     * Sets the name for this project.
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
     * Returns the name of this project.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets all files on this project.
     *
     * @param Collection $files
     *
     * @return void
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Returns all files with their sub-elements.
     *
     * @return Collection|FileDescriptor[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Sets all indexes for this project.
     *
     * An index is a compilation of references to elements, usually constructed in a compiler step, that aids template
     * generation by providing a conveniently assembled list. An example of such an index is the 'marker' index where
     * a list of TODOs and FIXMEs are located in a central location for reporting.
     *
     * @param Collection $indexes
     *
     * @return void
     */
    public function setIndexes(Collection $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * Returns all indexes in this project.
     *
     * @see setIndexes() for more information on what indexes are.
     *
     * @return Collection
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Sets the root namespace for this project together with all sub-namespaces.
     *
     * @param NamespaceDescriptor $namespace
     *
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the root (global) namespace.
     *
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
     * Sets all partials that can be used in a template.
     *
     * Partials are blocks of text that can be inserted anywhere in a template using a special indicator. An example is
     * the introduction partial that can add a custom piece of text to the homepage.
     *
     * @param Collection $partials
     *
     * @return void
     */
    public function setPartials(Collection $partials)
    {
        $this->partials = $partials;
    }

    /**
     * Returns a list of all partials.
     *
     * @see setPartials() for more information on partials.
     *
     * @return Collection
     */
    public function getPartials()
    {
        return $this->partials;
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
