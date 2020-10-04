<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

final class ParseQueue implements \IteratorAggregate
{
    /**
     * @var string[]
     */
    private $files = [];

    public function add(string $filename) : void
    {
        if (in_array($filename, $this->files, true)) {
            return;
        }

        $this->files[] = $filename;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }
}
