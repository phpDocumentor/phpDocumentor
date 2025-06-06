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

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;

/**
 * Represents the public interface to which all descriptors should be held.
 */
interface ElementInterface extends Descriptor, TracksErrors, DocblockInterface
{
    /**
     * Returns the namespace for this element (defaults to global "\")
     *
     * @return NamespaceInterface|string
     */
    public function getNamespace();

    /**
     * Sets the namespace (name) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     *
     * @param NamespaceInterface|string $namespace
     */
    public function setNamespace($namespace): void;

    /**
     * Sets the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function setFullyQualifiedStructuralElementName(Fqsen $name): void;

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function getFullyQualifiedStructuralElementName(): Fqsen|null;

    /**
     * Sets the file and location for this element.
     */
    public function setLocation(FileInterface $file, Location $startLocation): void;

    /**
     * Sets this element's start location in the source file.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setStartLocation(Location $startLocation): void;

    /**
     * Returns the end location where the definition for this element can be found.
     */
    public function getEndLocation(): Location|null;

    /**
     * Sets this element's end location in the source file.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setEndLocation(Location $endLocation): void;

    /**
     * Returns the file location for this element relative to the project root.
     */
    public function getPath(): string;

    /**
     * Returns the line number where this element may be found.
     *
     * @see getPath() to find out in which file this element is found.
     */
    public function getLine(): int;

    /**
     * Sets the name of the package to which this element belongs.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param PackageInterface|string $package
     */
    public function setPackage($package): void;

    /**
     * Returns the package name for this element.
     */
    public function getPackage(): PackageInterface|null;
}
