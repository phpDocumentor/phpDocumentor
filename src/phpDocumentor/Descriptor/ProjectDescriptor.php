<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;
use phpDocumentor\Descriptor\Traits\HasNamespace;
use phpDocumentor\Descriptor\Traits\HasPackage;
use phpDocumentor\Reflection\Fqsen;

/**
 * Represents the entire project with its files, namespaces and indexes.
 *
 * @api
 * @package phpDocumentor\AST
 */
class ProjectDescriptor implements Interfaces\ProjectInterface, Descriptor
{
    use HasName;
    use HasDescription;
    use HasPackage;
    use HasNamespace;

    /** @var Collection<FileInterface> $files */
    private Collection $files;

    /** @var Collection<Collection<ElementInterface>> $indexes */
    private Collection $indexes;

    private Settings $settings;

    /** @var Collection<string> $partials */
    private Collection $partials;

    /** @var Collection<VersionDescriptor> $versions */
    private Collection $versions;

    /**
     * Initializes this descriptor.
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->setSettings(new Settings());

        $namespace = new NamespaceDescriptor();
        $namespace->setName('\\');
        $namespace->setFullyQualifiedStructuralElementName(new Fqsen('\\'));
        $this->setNamespace($namespace);

        $package = new PackageDescriptor();
        $package->setName('\\');
        $package->setFullyQualifiedStructuralElementName(new Fqsen('\\'));
        $this->setPackage($package);

        $this->setFiles(new Collection());
        $this->setIndexes(new Collection());

        $this->setPartials(new Collection());
        $this->versions = Collection::fromClassString(VersionDescriptor::class);
    }

    /**
     * Sets all files on this project.
     *
     * @param Collection<FileInterface> $files
     */
    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    /**
     * Returns all files with their sub-elements.
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
     *
     * @param Collection<Collection<ElementInterface>> $indexes
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
     *
     * @param Collection<string> $partials
     */
    public function setPartials(Collection $partials): void
    {
        $this->partials = $partials;
    }

    /**
     * Returns a list of all partials.
     *
     * @see setPartials() for more information on partials.
     *
     * @return Collection<string>
     */
    public function getPartials(): Collection
    {
        return $this->partials;
    }

    public function findElement(Fqsen $fqsen): ?ElementInterface
    {
        if (!isset($this->getIndexes()['elements'])) {
            return null;
        }

        return $this->getIndexes()['elements']->fetch((string) $fqsen);
    }

    /**
     * @return Collection<VersionDescriptor>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }
}
