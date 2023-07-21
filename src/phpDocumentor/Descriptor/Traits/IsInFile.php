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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Reflection\Location;

trait IsInFile
{
    /** @var FileInterface|null $fileDescriptor The file to which this element belongs; if applicable */
    protected FileInterface|null $fileDescriptor = null;

    /** @var int $line The line number on which this element occurs. */
    protected int $line = 0;

    protected Location|null $startLocation = null;
    protected Location|null $endLocation = null;

    /**
     * Sets the file and linenumber where this element is at.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setLocation(FileInterface $file, Location $startLocation): void
    {
        $this->setFile($file);
        $this->setStartLocation($startLocation);
    }

    /**
     * Returns the path to the file containing this element relative to the project's root.
     */
    public function getPath(): string
    {
        return $this->fileDescriptor ? $this->fileDescriptor->getPath() : '';
    }

    /**
     * Returns the file in which this element resides or null in case the element is not bound to a file..
     */
    public function getFile(): FileInterface|null
    {
        return $this->fileDescriptor;
    }

    /**
     * Sets the file to which this element is associated.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setFile(FileInterface $file): void
    {
        $this->fileDescriptor = $file;
    }

    /**
     * Returns the line number where the definition for this element can be found.
     *
     * @deprecated use getStartLocation()->getLineNumber() instead
     */
    public function getLine(): int
    {
        if ($this->getStartLocation() === null) {
            return 0;
        }

        return $this->getStartLocation()->getLineNumber();
    }

    /**
     * Returns the start location where the definition for this element can be found.
     */
    public function getStartLocation(): Location|null
    {
        return $this->startLocation;
    }

    /**
     * Sets this element's start location in the source file.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setStartLocation(Location $startLocation): void
    {
        $this->startLocation = $startLocation;
    }

    /**
     * Returns the end location where the definition for this element can be found.
     */
    public function getEndLocation(): Location|null
    {
        return $this->endLocation;
    }

    /**
     * Sets this element's end location in the source file.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setEndLocation(Location $endLocation): void
    {
        $this->endLocation = $endLocation;
    }
}
