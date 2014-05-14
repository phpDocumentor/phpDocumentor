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

namespace phpDocumentor\Configuration;

use JMS\Serializer\Annotation as Serializer;

/**
 * Configuration definition for the logger.
 */
class Logging
{
    /**
     * @var string the minimum output level for logging
     * @Serializer\Type("string")
     */
    protected $level;

    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     */
    protected $paths = array(
        'default' => null,
        'errors'  => null
    );

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return mixed
     */
    public function getPaths()
    {
        return $this->paths;
    }
}
