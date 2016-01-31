<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Version;

/**
 * A value object for versionNumbers
 */
final class Number
{
    /**
     * String containing the version this object represents
     * @var string
     */
    private $version;

    /**
     * Initializes the object.
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Returns the string representation of this version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the string representation of this version.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getVersion();
    }
}
