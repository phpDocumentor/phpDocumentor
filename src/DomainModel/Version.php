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

namespace phpDocumentor\DomainModel;

use phpDocumentor\DomainModel\VersionNumber;

/**
 * Version Entity
 */
final class Version
{
    /**
     * number of this version.
     *
     * @var VersionNumber
     */
    private $versionNumber;

    /**
     * Initializes the object.
     *
     * @param VersionNumber $versionNumber
     */
    public function __construct(VersionNumber $versionNumber)
    {
        $this->versionNumber = $versionNumber;
    }

    /**
     * Return number of this version.
     *
     * @return VersionNumber
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }
}
