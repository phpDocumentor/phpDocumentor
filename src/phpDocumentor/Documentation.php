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
     * Title of the documentation.
     *
     * @var string
     */
    private $title;

    /**
     * Version number of the documentation.
     *
     * @var VersionNumber
     */
    private $versionNumber;

    /**
     * @var DocumentGroup[]
     */
    private $documentGroups;

    /**
     * Initializes the documentation object.
     *
     * @param string $title
     * @param VersionNumber $versionNumber
     * @param DocumentGroup[] $documentGroups
     */
    public function __construct($title, VersionNumber $versionNumber, array $documentGroups = array())
    {
        $this->title = $title;
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
     * Returns the title of the documentation.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
