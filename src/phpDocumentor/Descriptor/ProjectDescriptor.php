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

use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\PackageInterface;
use phpDocumentor\Descriptor\Interfaces\VersionInterface;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;
use phpDocumentor\ProjectSettings;
use Webmozart\Assert\Assert;

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

    private ProjectSettings $settings;

    /** @var Collection<string> $partials */
    private Collection $partials;

    /** @var Collection<VersionInterface> $versions */
    private readonly Collection $versions;

    /**
     * Initializes this descriptor.
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->setSettings(new ProjectSettings());
        $this->setPartials(new Collection());
        $this->versions = Collection::fromInterfaceString(VersionInterface::class);
    }

    /**
     * Returns all files with their sub-elements.
     *
     * @deprecated Please use {@see DocumentationSetDescriptor::getFiles()}
     *
     * @return Collection<FileInterface>
     */
    public function getFiles(): Collection
    {
        return $this->getApiDocumentationSet()->getFiles();
    }

    /**
     * Returns all indexes in this project.
     *
     * @deprecated Please use {@see DocumentationSetDescriptor::getIndexes()}
     *
     * @return CollectionInterface<CollectionInterface<ElementInterface>>
     */
    public function getIndexes(): CollectionInterface
    {
        return $this->getApiDocumentationSet()->getIndexes();
    }

    /**
     * Sets the settings used to build the documentation for this project.
     */
    public function setSettings(ProjectSettings $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Returns the settings used to build the documentation for this project.
     */
    public function getSettings(): ProjectSettings
    {
        return $this->settings;
    }

    /**
     * Sets all partials that can be used in a template.
     *
     * Partials are blocks of text that can be inserted anywhere in a template using a special indicator. An example is
     * the introduction partial that can add a custom piece of text to the homepage.
     *
     * @param CollectionInterface<string> $partials
     */
    public function setPartials(CollectionInterface $partials): void
    {
        $this->partials = $partials;
    }

    /**
     * Returns a list of all partials.
     *
     * @see setPartials() for more information on partials.
     *
     * @return CollectionInterface<string>
     */
    public function getPartials(): CollectionInterface
    {
        return $this->partials;
    }

    /** @return Collection<VersionInterface> */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    /**
     * Sets the namespace (name) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     * @deprecated
     *
     * @param NamespaceInterface|string $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->getApiDocumentationSet()->setNamespace($namespace);
    }

    /**
     * Returns the namespace for this element (defaults to global "\")
     *
     * @deprecated
     *
     * @return NamespaceInterface|string
     */
    public function getNamespace()
    {
        return $this->getApiDocumentationSet()->getNamespace();
    }

    /**
     * Sets the package (name) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     * @deprecated
     *
     * @param PackageInterface|string $package
     */
    public function setPackage($package): void
    {
        $this->getApiDocumentationSet()->setPackage($package);
    }

    /**
     * Returns the package for this element (defaults to global "\")
     *
     * @deprecated
     */
    public function getPackage(): PackageInterface|null
    {
        return $this->getApiDocumentationSet()->getPackage();
    }

    /**
     * Retrieves the first API Documentation set from the first version.
     *
     * @deprecated As soon as we are done migrating to multiple API Documentation sets, this method becomes invalid
     *     and should be removed.
     */
    private function getApiDocumentationSet(): ApiSetDescriptor
    {
        $firstVersion = $this->versions->first();
        Assert::isInstanceOf($firstVersion, VersionDescriptor::class);

        $firstApiSet = $firstVersion->getDocumentationSets()->filter(ApiSetDescriptor::class)->first();
        Assert::isInstanceOf($firstApiSet, ApiSetDescriptor::class);

        return $firstApiSet;
    }
}
