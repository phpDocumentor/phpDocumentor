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
     * @var VersionNumber
     */
    private $versionNumber;

    /**
     * @param string $title
     * @param VersionNumber $versionNumber
     */
    public function __construct($title, VersionNumber $versionNumber)
    {
        $this->versionNumber = $versionNumber;
    }

    /**
     * @return VersionNumber
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }
}
