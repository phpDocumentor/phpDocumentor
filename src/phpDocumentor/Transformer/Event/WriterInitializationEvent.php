<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;
use phpDocumentor\Transformer\Writer\WriterAbstract;

final class WriterInitializationEvent extends EventAbstract
{
    /** @var WriterAbstract|null */
    private $writer;

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     */
    public static function createInstance(object $subject): EventAbstract
    {
        return new self($subject);
    }

    /**
     * Sets the currently parsed writer in this event.
     */
    public function setWriter(WriterAbstract $writer): WriterInitializationEvent
    {
        $this->writer = $writer;

        return $this;
    }

    public function getWriter(): ?WriterAbstract
    {
        return $this->writer;
    }
}
