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

namespace phpDocumentor\Guides\RestructuredText\Nodes;

use phpDocumentor\Guides\Nodes\Node;

final class SidebarNode extends Node
{
    /** @var string */
    private $title;

    public function __construct(string $title, ?Node $value = null)
    {
        parent::__construct($value);
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
