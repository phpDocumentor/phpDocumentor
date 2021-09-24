<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Iterator;
use phpDocumentor\Guides\Environment;

/**
 * @implements Iterator<string>
 */
class DocumentIterator implements Iterator
{
    /** @var string[] */
    private $lines = [];

    /** @var int */
    private $position = 0;

    public function load(Environment $environment, string $document): void
    {
        $document = $this->prepareDocument($environment, $document);
        $this->lines = explode("\n", $document);
        $this->rewind();
    }

    public function getNextLine(): string
    {
        return $this->lines[$this->position + 1] ?? '';
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): string
    {
        return $this->lines[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->lines[$this->position]);
    }

    private function prepareDocument(Environment $environment, string $document): string
    {
        $document = str_replace("\r\n", "\n", $document);
        $document = sprintf("\n%s\n", $document);

        $document = $this->mergeIncludedFiles($environment, $document);

        // Removing UTF-8 BOM
        $document = str_replace("\xef\xbb\xbf", '', $document);

        // Replace \u00a0 with " "
        $document = str_replace(chr(194) . chr(160), ' ', $document);

        return $document;
    }

    public function mergeIncludedFiles(Environment $environment, string $document): string
    {
        return preg_replace_callback(
            '/^\.\. include:: (.+)$/m',
            function ($match) use ($environment) {
                $path = $environment->absoluteRelativePath($match[1]);

                $origin = $environment->getOrigin();
                if (!$origin->has($path)) {
                    throw new \RuntimeException(
                        sprintf('Include "%s" (%s) does not exist or is not readable.', $match[0], $path)
                    );
                }

                $contents = $origin->read($path);

                if ($contents === false) {
                    throw new \RuntimeException(sprintf('Could not load file from path %s', $path));
                }

                return $this->mergeIncludedFiles($environment, $contents);
            },
            $document
        );
    }
}
