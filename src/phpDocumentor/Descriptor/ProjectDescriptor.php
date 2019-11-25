<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Represents the entire project with its files, namespaces and indexes.
 */
class ProjectDescriptor implements Interfaces\ProjectInterface, Descriptor
{
    /** @var string $name */
    protected $name = '';

    /** @var NamespaceDescriptor $namespace */
    protected $namespace;

    /** @var Collection $files */
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
    public function __construct(string $name)
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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the name of this project.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the description for this element.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * Sets all files on this project.
     *
     * @param Collection $files
     */
    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    /**
     * Returns all files with their sub-elements.
     *
     * @return Collection|FileDescriptor[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * Sets all indexes for this project.
     *
     * An index is a compilation of references to elements, usually constructed in a compiler step, that aids template
     * generation by providing a conveniently assembled list. An example of such an index is the 'marker' index where
     * a list of TODOs and FIXMEs are located in a central location for reporting.
     */
    public function setIndexes(Collection $indexes): void
    {
        $this->indexes = $indexes;
    }

    /**
     * Returns all indexes in this project.
     *
     * @see setIndexes() for more information on what indexes are.
     */
    public function getIndexes(): Collection
    {
        return $this->indexes;
    }

    /**
     * Sets the root namespace for this project together with all sub-namespaces.
     */
    public function setNamespace(NamespaceDescriptor $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the root (global) namespace.
     */
    public function getNamespace(): NamespaceInterface
    {
        return $this->namespace;
    }

    /**
     * Sets the settings used to build the documentation for this project.
     */
    public function setSettings(Settings $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Returns the settings used to build the documentation for this project.
     */
    public function getSettings(): Settings
    {
        return $this->settings;
    }

    /**
     * Sets all partials that can be used in a template.
     *
     * Partials are blocks of text that can be inserted anywhere in a template using a special indicator. An example is
     * the introduction partial that can add a custom piece of text to the homepage.
     */
    public function setPartials(Collection $partials): void
    {
        $this->partials = $partials;
    }

    /**
     * Returns a list of all partials.
     *
     * @see setPartials() for more information on partials.
     */
    public function getPartials(): Collection
    {
        return $this->partials;
    }

    /**
     * Checks whether the Project supports the given visibility.
     *
     * @param integer $visibility One of the VISIBILITY_* constants of the Settings class.
     *
     * @see Settings for a list of the available VISIBILITY_* constants.
     */
    public function isVisibilityAllowed(int $visibility): bool
    {
        $visibilityAllowed = $this->getSettings()->getVisibility();

        return (bool) ($visibilityAllowed & $visibility);
    }
}
