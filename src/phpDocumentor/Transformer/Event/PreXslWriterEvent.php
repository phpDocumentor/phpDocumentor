<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;

/**
 * Event launched during the Xsl Transformation of an individual output file.
 *
 * If the XSL Writer loops through a resultset then this event will be thrown
 * for each result.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class PreXslWriterEvent extends EventAbstract
{
    /** @var \DOMElement */
    protected $element;

    /** @var int[] */
    protected $progress = [0, 0];

    /**
     * Sets the currently parsed element in this event.
     *
     * @param \DOMElement $element
     *
     * @return PreXslWriterEvent
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Returns the event that is currently being parsed.
     *
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Sets a progress indication for this XSL Writer session.
     *
     * @param int[] $progress Array containing 2 integer values, the current
     *     step in the process and the total number of steps involved.
     *
     * @return PreXslWriterEvent
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Returns the current step and tot number of steps as progress.
     *
     * @see setProgress() for a complete description of this array.
     *
     * @return int[]
     */
    public function getProgress()
    {
        return $this->progress;
    }
}
