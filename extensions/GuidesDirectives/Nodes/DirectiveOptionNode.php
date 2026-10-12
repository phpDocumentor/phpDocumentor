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

namespace phpDocumentor\GuidesDirectives\Nodes;

use phpDocumentor\Guides\Nodes\CompoundNode;
use phpDocumentor\Guides\Nodes\Node;

/** @extends CompoundNode<Node> */
final class DirectiveOptionNode extends CompoundNode
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly string|null $default,
        private readonly string $description,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDefault(): string|null
    {
        return $this->default;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
