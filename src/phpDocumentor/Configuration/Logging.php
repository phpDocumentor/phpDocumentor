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
use Psr\Log\LogLevel;

/**
 * Configuration definition for the logger.
 */
class Logging
{
    /**
     * @var string the minimum output level for logging.
     *
     * @Serializer\Type("string")
     */
    protected $level = LogLevel::ERROR;

    /**
     * @var string[] the paths determining where to output log files.
     *
     * @Serializer\Type("array<string>")
     */
    protected $paths = array(
        'default' => null,
        'errors'  => null
    );

    /**
     * Returns the minimum output level for logging.
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the minimum output level for the logger.
     *
     * @param string $level
     *
     * @return void
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Returns the paths that determine where to store log files.
     *
     * phpDocumentor uses two types of log files to be able to sift through the logs more easily:
     *
     * - 'default', contains all logs as mentioned in the logging level in this object and
     * - 'debug', contains debugging information that is exposed when debugging is enabled.
     *
     * @return string[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Registers the paths that determine where to store log files.
     *
     * @param \string[] $paths
     *
     * @see getPaths() for more information.
     *
     * @return void
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }
}
