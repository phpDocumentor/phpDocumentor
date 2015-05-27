<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Project;


final class VersionNumber
{
    /**
     * String containing the version this object represents
     * @var string
     */
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }
}