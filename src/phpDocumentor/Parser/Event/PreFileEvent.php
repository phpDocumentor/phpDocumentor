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

namespace phpDocumentor\Parser\Event;

use phpDocumentor\Event\EventAbstract;

/**
 * Event thrown before the parsing of an individual file.
 */
final class PreFileEvent extends EventAbstract
{
    /** @var string */
    private $file = '';

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     *
     * @return self
     */
    public static function createInstance(object $subject): EventAbstract
    {
        return new self($subject);
    }

    /**
     * Sets the name of the file that is about to be processed.
     */
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Returns the name of the file that is about to be processed.
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
