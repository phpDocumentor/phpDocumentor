<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

use function array_reduce;
use function trim;

/**
 * Represents a single item of a bullet or enumerated list.
 */
final class ListItemNode extends Node
{
    /** @var string the list marker used for this item */
    private $prefix;

    /** @var bool whether the list marker represents an enumerated list */
    private $ordered;

    /** @var Node[] */
    private $contents;

    /**
     * @param Node[] $contents
     */
    public function __construct(string $prefix, bool $ordered, array $contents)
    {
        $this->prefix   = $prefix;
        $this->ordered  = $ordered;
        $this->contents = $contents;

        parent::__construct(null);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function isOrdered(): bool
    {
        return $this->ordered;
    }

    /**
     * @return Node[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    public function getContentsAsString(): string
    {
        return trim(array_reduce($this->contents, static function (string $contents, Node $node): string {
            return $contents . $node->getValueString() . "\n";
        }, ''));
    }
}
