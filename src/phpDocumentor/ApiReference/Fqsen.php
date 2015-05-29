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

namespace phpDocumentor\ApiReference;

/**
 * Class Fqsen
 */
final class Fqsen
{
    /**
     * @var string full quallified class name
     */
    private $fqsen;

    /**
     * @param string $fqsen
     */
    public function __construct($fqsen)
    {
        $this->fqsen = $fqsen;
    }

    /**
     * converts this class to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fqsen;
    }
}
