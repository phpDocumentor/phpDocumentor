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

use phpDocumentor\Guides\RestructuredText\Parser\ListItem;

class ListNode extends Node
{
    /** @var bool */
    private $ordered;

    /** @var ListItem[] */
    private $items;

    /**
     * @param ListItem[] $items
     */
    public function __construct(array $items, bool $ordered)
    {
        parent::__construct();

        $this->items   = $items;
        $this->ordered = $ordered;
    }

    /**
     * @return ListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function isOrdered(): bool
    {
        return $this->ordered;
    }
}
