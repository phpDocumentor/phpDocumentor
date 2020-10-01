<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class ListNode extends Node
{
    /** @var mixed[][] */
    protected $lines = [];

    /**
     * Infos contains:
     *
     * - text: the line text
     * - depth: the depth in the list level
     * - prefix: the prefix char (*, - etc.)
     * - ordered: true of false if the list is ordered
     *
     * @param mixed[] $line
     */
    public function addLine(array $line) : void
    {
        $this->lines[] = $line;
    }

    /**
     * @return mixed[][]
     */
    public function getLines() : array
    {
        return $this->lines;
    }
}
