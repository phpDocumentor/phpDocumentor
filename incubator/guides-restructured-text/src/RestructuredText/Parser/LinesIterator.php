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

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Iterator;
use OutOfBoundsException;

use function chr;
use function explode;
use function preg_replace_callback;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strlen;
use function trim;

/**
 * @implements Iterator<string>
 */
class LinesIterator implements Iterator
{
    /** @var string[] */
    private array $lines = [];
    private int $position = 0;

    public function load(string $document): void
    {
        $document = trim($this->prepareDocument($document));
        $this->lines = explode("\n", $document);

        foreach ($this->lines as $key => $line) {
            $this->lines[$key] = $this->tabToSpaces($line);
        }

        $this->rewind();
    }

    private function tabToSpaces(string $line): string
    {
        $tabStop = 8;

        return preg_replace_callback(
            '/^(\t*)/',
            static fn ($matches) => str_repeat(' ', strlen($matches[1]) * $tabStop),
            $line
        );
    }

    public function getNextLine(): ?string
    {
        return $this->lines[$this->position + 1] ?? null;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): string
    {
        if ($this->valid() === false) {
            throw new OutOfBoundsException('Attempted to read a line that does not exist');
        }

        return $this->lines[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    /**
     * @deprecated Work around for Production's eating one line too many
     *
     * @todo Revisit The Loop in {@see DocumentParser::parseLines()} and see if the Look Ahead timing should be done
     *       differently
     */
    public function prev(): void
    {
        --$this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function atStart(): bool
    {
        return $this->position === 0;
    }

    public function valid(): bool
    {
        return isset($this->lines[$this->position]);
    }

    private function prepareDocument(string $document): string
    {
        $document = str_replace("\r\n", "\n", $document);
        $document = sprintf("\n%s\n", $document);

        // Removing UTF-8 BOM
        $document = str_replace("\xef\xbb\xbf", '', $document);

        // Replace \u00a0 with " "
        $document = str_replace(chr(194) . chr(160), ' ', $document);

        return $document;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->lines;
    }
}
