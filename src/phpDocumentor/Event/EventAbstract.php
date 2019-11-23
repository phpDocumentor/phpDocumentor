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

namespace phpDocumentor\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract class representing the base elements of a phpDocumentor event.
 */
abstract class EventAbstract extends Event
{
    /** @var object Represents an object that is the subject of this event */
    protected $subject;

    /**
     * Initializes this event with the given subject.
     *
     * @param object $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the object that is the subject of this event.
     *
     * @return object
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     *
     * @param object $subject
     */
    public static function createInstance($subject): self
    {
        return new static($subject);
    }
}
