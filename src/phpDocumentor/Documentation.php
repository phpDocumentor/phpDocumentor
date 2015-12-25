<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use phpDocumentor\Project\VersionNumber;

/**
 * Documentation class containing one or more DocumentGroups.
 */
final class Documentation
{
    /**
     * Version number of the documentation.
     *
     * @var VersionNumber
     */
    private $versionNumber;

    /**
     * Document groups of this documentation.
     *
     * @var DocumentGroup[]
     */
    private $documentGroups;

    /**
     * Initializes the documentation object.
     *
     * @param VersionNumber $versionNumber
     * @param DocumentGroup[] $documentGroups
     */
    public function __construct(VersionNumber $versionNumber, array $documentGroups = array())
    {
        $this->versionNumber = $versionNumber;
        $this->documentGroups = $documentGroups;
    }

    /**
     * Returns the versionNumber of the documentation.
     *
     * @return VersionNumber
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

    /**
     * Returns the DocumentGroups included in this documentation.
     *
     * @return DocumentGroup[]
     */
    public function getDocumentGroups()
    {
        return $this->documentGroups;
    }
}
