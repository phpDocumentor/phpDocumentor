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

/**
 * Value Object for paths.
 * This can be absolute or relative.
 */
final class Path
{
    /**
     * file path
     *
     * @var string
     */
    private $path;

    /**
     * Initializes the path.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = (string)$path;
    }

    /**
     * returns a string representation of the path.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->path;
    }
}