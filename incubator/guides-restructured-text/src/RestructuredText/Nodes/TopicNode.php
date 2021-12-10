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

final class TopicNode extends Node
{
    /** @var string */
    private $name;

    public function __construct(string $name, ?Node $value = null)
    {
        parent::__construct($value);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
