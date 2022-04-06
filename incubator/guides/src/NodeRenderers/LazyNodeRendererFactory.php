<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\NodeRenderers;

use phpDocumentor\Guides\Nodes\Node;

final class LazyNodeRendererFactory implements NodeRendererFactory
{
    /** @var callable */
    private $factory;

    private ?NodeRendererFactory $innerFactory = null;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function get(Node $node): NodeRenderer
    {
        if ($this->innerFactory === null) {
            $this->innerFactory = ($this->factory)();
        }

        return $this->innerFactory->get($node);
    }
}
