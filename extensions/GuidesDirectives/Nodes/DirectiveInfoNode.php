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

/** @extends CompoundNode<DirectiveOptionNode> */
final class DirectiveInfoNode extends CompoundNode
{
    /**
     * @param string[] $aliases
     * @param DirectiveOptionNode[] $options
     */
    public function __construct(
        private readonly string $name,
        private readonly array $aliases,
        private readonly string $description,
        array $options = [],
    ) {
        parent::__construct($options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAnchor(): string
    {
        return 'directive-' . $this->name;
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
