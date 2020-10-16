<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use function array_pop;
use function count;
use function implode;

class Buffer
{
    /** @var string[] */
    private $lines;

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines = [])
    {
        $this->lines = $lines;
    }

    public function isEmpty() : bool
    {
        return $this->lines === [];
    }

    public function count() : int
    {
        return count($this->lines);
    }

    public function has(int $key) : bool
    {
        return isset($this->lines[$key]);
    }

    public function get(int $key) : string
    {
        return $this->lines[$key] ?? '';
    }

    public function push(string $line) : void
    {
        $this->lines[] = $line;
    }

    public function set(int $key, string $line) : void
    {
        $this->lines[$key] = $line;
    }

    /**
     * @return string[]
     */
    public function getLines() : array
    {
        return $this->lines;
    }

    public function getLinesString() : string
    {
        return implode("\n", $this->lines);
    }

    public function pop() : ?string
    {
        return array_pop($this->lines);
    }

    public function getLastLine() : ?string
    {
        $lastLineKey = count($this->lines) - 1;

        if (!isset($this->lines[$lastLineKey])) {
            return null;
        }

        return $this->lines[$lastLineKey];
    }
}
