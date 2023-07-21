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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\VersionDescriptor;

/**
 * Describes the public interface for the description of a project.
 */
interface ProjectInterface
{
    public function setName(string $name): void;

    public function getName(): string;

    /**
     * @deprecated Please use the getFiles method on the DocumentationSet
     *
     * @return Collection<FileInterface>
     */
    public function getFiles(): Collection;

    /**
     * @deprecated Please use the getIndexes method on the DocumentationSet
     *
     * @return Collection<Collection<ElementInterface>>
     */
    public function getIndexes(): Collection;

    /**
     * Returns the package name for this element.
     */
    public function getPackage(): PackageInterface|null;

    /** @return NamespaceInterface|string */
    public function getNamespace();

    /** @return Collection<VersionDescriptor> */
    public function getVersions(): Collection;
}
