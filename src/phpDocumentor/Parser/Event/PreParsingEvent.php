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
 * Event thrown before the parsing all files.
 */
final class PreParsingEvent extends EventAbstract
{
    /** @var int */
    private $fileCount;

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     */
    public static function createInstance(object $subject): EventAbstract
    {
        return new self($subject);
    }

    public function setFileCount(int $fileCount): self
    {
        $this->fileCount = $fileCount;

        return $this;
    }

    public function getFileCount(): int
    {
        return $this->fileCount;
    }
}
