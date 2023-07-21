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

namespace phpDocumentor\Transformer\Writer;

use InvalidArgumentException;

use function array_key_exists;

/**
 * A collection of Writer objects.
 *
 * In this collection we can receive writers.
 *
 * In addition, this class can also verify if all requirements for the various writers in it are met.
 */
final class Collection
{
    /** @var array<string, WriterAbstract> */
    private array $writers = [];

    /** @param iterable<string, WriterAbstract> $writers */
    public function __construct(iterable $writers = [])
    {
        foreach ($writers as $writer) {
            $this->register($writer);
        }
    }

    public function register(WriterAbstract $writer): void
    {
        $this->writers[$writer->getName()] = $writer;
    }

    /**
     * Retrieves a writer from the collection.
     *
     * @param string $index the name of the writer to retrieve.
     *
     * @throws InvalidArgumentException If the writer is not in the collection.
     */
    public function get(string $index): WriterAbstract
    {
        if (array_key_exists($index, $this->writers) === false) {
            throw new InvalidArgumentException('Writer "' . $index . '" does not exist');
        }

        return $this->writers[$index];
    }

    /**
     * Iterates over each writer in this collection and checks its requirements.
     *
     * @throws Exception\RequirementMissing If a requirement of a writer is missing.
     */
    public function checkRequirements(): void
    {
        /** @var WriterAbstract $writer */
        foreach ($this->writers as $writer) {
            $writer->checkRequirements();
        }
    }
}
