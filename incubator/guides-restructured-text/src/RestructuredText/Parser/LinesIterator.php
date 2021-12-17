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
use phpDocumentor\Guides\ParserContext;
use RuntimeException;

use function chr;
use function explode;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
use function trim;

/**
 * @implements Iterator<string>
 */
class LinesIterator implements Iterator
{
    /** @var string[] */
    private $lines = [];

    /** @var int */
    private $position = 0;

    public function load(ParserContext $parserContext, string $document): void
    {
        $document = trim($this->prepareDocument($parserContext, $document));
        $this->lines = explode("\n", $document);
        $this->rewind();
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

    private function prepareDocument(ParserContext $parserContext, string $document): string
    {
        $document = str_replace("\r\n", "\n", $document);
        $document = sprintf("\n%s\n", $document);

        $document = $this->mergeIncludedFiles($parserContext, $document);

        // Removing UTF-8 BOM
        $document = str_replace("\xef\xbb\xbf", '', $document);

        // Replace \u00a0 with " "
        $document = str_replace(chr(194) . chr(160), ' ', $document);

        return $document;
    }

    private function mergeIncludedFiles(ParserContext $parserContext, string $document): string
    {
        return preg_replace_callback(
            '/^\.\. include:: (.+)$/m',
            function ($match) use ($parserContext) {
                $path = $parserContext->absoluteRelativePath($match[1]);

                $origin = $parserContext->getOrigin();
                if (!$origin->has($path)) {
                    throw new RuntimeException(
                        sprintf('Include "%s" (%s) does not exist or is not readable.', $match[0], $path)
                    );
                }

                $contents = $origin->read($path);

                if ($contents === false) {
                    throw new RuntimeException(sprintf('Could not load file from path %s', $path));
                }

                return $this->mergeIncludedFiles($parserContext, $contents);
            },
            $document
        );
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->lines;
    }
}
