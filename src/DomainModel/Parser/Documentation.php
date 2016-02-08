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

namespace phpDocumentor\DomainModel\Parser;

use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup;
use phpDocumentor\DomainModel\Parser\Version\Number;

/**
 * Documentation class containing one or more DocumentGroups.
 */
final class Documentation
{
    /**
     * Version number of the documentation.
     *
     * @var Number
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
     * @param Number $versionNumber
     * @param DocumentGroup[] $documentGroups
     */
    public function __construct(Number $versionNumber, array $documentGroups = array())
    {
        $this->versionNumber = $versionNumber;
        $this->documentGroups = $documentGroups;
    }

    /**
     * Returns the versionNumber of the documentation.
     *
     * @return Number
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
