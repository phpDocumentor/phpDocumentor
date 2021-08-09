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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the example tag.
 *
 * @api
 * @package phpDocumentor\AST\Tags
 */
final class ExampleDescriptor extends TagDescriptor
{
    /** @var string $filePath the content of the example. */
    private $filePath = '';

    /** @var int|null $startingLine the content of the example. */
    private $startingLine;

    /** @var int|null $lineCount the content of the example. */
    private $lineCount;

    /** @var string $example the content of the example. */
    private $example = '';

    /**
     * Sets the location where the example points to.
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * Returns the location where this example points to.
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Returns the location where this example points to.
     */
    public function setStartingLine(int $startingLine): void
    {
        $this->startingLine = $startingLine;
    }

    /**
     * Returns the location where this example points to.
     */
    public function getStartingLine(): ?int
    {
        return $this->startingLine;
    }

    /**
     * Returns the location where this example points to.
     */
    public function setLineCount(int $lineCount): void
    {
        $this->lineCount = $lineCount;
    }

    /**
     * Returns the location where this example points to.
     */
    public function getLineCount(): ?int
    {
        return $this->lineCount;
    }

    /**
     * Returns the content of the example.
     */
    public function setExample(string $example): void
    {
        $this->example = $example;
    }

    /**
     * Returns the content of the example.
     */
    public function getExample(): string
    {
        return $this->example;
    }
}
