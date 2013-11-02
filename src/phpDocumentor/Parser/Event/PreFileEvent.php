<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Event;

use phpDocumentor\Event\EventAbstract;

/**
 * Event thrown before the parsing of an individual file.
 */
class PreFileEvent extends EventAbstract
{
    /** @var string */
    protected $file;

    /**
     * Sets the name of the file that is about to be processed.
     *
     * @param string $file
     *
     * @return self|PreFileEvent
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Returns the name of the file that is about to be processed.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}
