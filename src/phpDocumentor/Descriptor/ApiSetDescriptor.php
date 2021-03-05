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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\Source;
use phpDocumentor\Descriptor\Interfaces\PackageInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\File;

final class ApiSetDescriptor extends DocumentationSetDescriptor implements Descriptor
{
    /** @var Collection<FileDescriptor> */
    private $files;

    /** @var Collection<NamespaceDescriptor> */
    private $namespaces;

    /** @var Collection<Collection<DescriptorAbstract>> */
    private $indexes;

    /** @var NamespaceDescriptor */
    private $namespace;

    /** @var PackageDescriptor */
    private $package;

    /** @var ApiSpecification */
    private $apiSpecification;

    public function __construct(
        string $name,
        Source $source,
        string $outputLocation,
        ApiSpecification $apiSpecification
    ) {
        parent::__construct();
        $this->name = $name;
        $this->source = $source;
        $this->outputLocation = $outputLocation;
        $this->indexes = Collection::fromClassString(Collection::class);
        $this->files = Collection::fromClassString(FileDescriptor::class);
        $this->namespaces = Collection::fromClassString(NamespaceDescriptor::class);

        $namespace = new NamespaceDescriptor();
        $namespace->setName('\\');
        $namespace->setFullyQualifiedStructuralElementName(new Fqsen('\\'));
        $this->namespace = $namespace;

        $package = new PackageDescriptor();
        $package->setName('\\');
        $package->setFullyQualifiedStructuralElementName(new Fqsen('\\'));
        $this->package = $package;
        $this->apiSpecification = $apiSpecification;
    }

    public function addFile(FileDescriptor $descriptor): void
    {
        $this->files->set($descriptor->getPath(), $descriptor);
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addNamespace(NamespaceDescriptor $descriptor): void
    {
        $this->namespaces->set((string)$descriptor->getFullyQualifiedStructuralElementName(), $descriptor);
    }

    public function getNamespaces(): Collection
    {
        return $this->namespaces;
    }

    /**
     * Returns all indexes in this project.
     *
     * @see setIndexes() for more information on what indexes are.
     *
     * @return Collection<Collection<DescriptorAbstract>>
     */
    public function getIndexes(): Collection
    {
        return $this->indexes;
    }

    /**
     * Returns the root (global) namespace.
     */
    public function getNamespace(): NamespaceDescriptor
    {
        return $this->namespace;
    }

    public function getPackage(): PackageInterface
    {
        return $this->package;
    }

    public function getSettings(): ApiSpecification
    {
        return $this->apiSpecification;
    }

    public function getDescription(): ?DocBlock\DescriptionDescriptor
    {
        return null;
    }
}
