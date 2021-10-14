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

namespace phpDocumentor\Guides\Nodes;

class CodeNode extends Node
{
    /** @var bool */
    protected $raw = false;

    /** @var string|null */
    protected $language;

    /** @var int|null The line number to start counting from and display, or null to hide line numbers */
    private $startingLineNumber;

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        parent::__construct($this->normalizeLines($lines));
    }

    public function setLanguage(?string $language = null): void
    {
        $this->language = $language;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setStartingLineNumber(?int $lineNumber): void
    {
        $this->startingLineNumber = $lineNumber;
    }

    public function getStartingLineNumber(): ?int
    {
        return $this->startingLineNumber;
    }

    public function setRaw(bool $raw): void
    {
        $this->raw = $raw;
    }

    public function isRaw(): bool
    {
        return $this->raw;
    }
}
