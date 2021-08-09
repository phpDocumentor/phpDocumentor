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

namespace phpDocumentor\Guides\Nodes\Table;

use LogicException;
use phpDocumentor\Guides\Nodes\Node;

use function strlen;
use function trim;

final class TableColumn
{
    /** @var string */
    private $content;

    /** @var int */
    private $colSpan;

    /** @var int */
    private $rowSpan = 1;

    /** @var Node|null */
    private $node;

    public function __construct(string $content, int $colSpan)
    {
        $this->content = trim($content);
        $this->colSpan = $colSpan;
    }

    public function getContent(): string
    {
        // "\" is a special way to make a column "empty", but
        // still indicate that you *want* that column
        if ($this->content === '\\') {
            return '';
        }

        return $this->content;
    }

    public function getColSpan(): int
    {
        return $this->colSpan;
    }

    public function getRowSpan(): int
    {
        return $this->rowSpan;
    }

    public function addContent(string $content): void
    {
        $this->content = trim($this->content . $content);
    }

    public function incrementRowSpan(): void
    {
        $this->rowSpan++;
    }

    public function getNode(): Node
    {
        if ($this->node === null) {
            throw new LogicException('The node is not yet set.');
        }

        return $this->node;
    }

    public function setNode(Node $node): void
    {
        $this->node = $node;
    }

    /**
     * Indicates that a column is empty, and could be skipped entirely.
     */
    public function isCompletelyEmpty(): bool
    {
        return strlen($this->content) === 0;
    }
}
