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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;

/**
 * Represents the public interface to which all descriptors should be held.
 */
interface ElementInterface
{
    /**
     * Sets the Fully Qualified Structural Element Name (FQSEN) for this element.
     *
     * @param string $name
     *
     * @return void
     */
    public function setFullyQualifiedStructuralElementName($name);

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     *
     * @return string
     */
    public function getFullyQualifiedStructuralElementName();

    /**
     * Sets the local name for this element.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * Returns the local name for this element.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets a summary describing this element.
     *
     * @param string $summary
     *
     * @return void
     */
    public function setSummary($summary);

    /**
     * Returns the summary describing this element.
     *
     * @return string
     */
    public function getSummary();

    /**
     * Sets a longer description for this element.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description);

    /**
     * Returns a longer description for this element.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the file and location for this element.
     *
     * @param FileDescriptor $file
     * @param int            $line
     *
     * @return void
     */
    public function setLocation(FileDescriptor $file, $line = 0);

    /**
     * Returns the file location for this element relative to the project root.
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the line number where this element may be found.
     *
     * @see getPath() to find out in which file this element is found.
     *
     * @return int
     */
    public function getLine();

    /**
     * Returns all tags associated with this element.
     *
     * @return Collection
     */
    public function getTags();
}
