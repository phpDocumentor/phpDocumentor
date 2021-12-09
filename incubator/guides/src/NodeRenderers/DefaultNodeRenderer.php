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

namespace phpDocumentor\Guides\NodeRenderers;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;

use function is_callable;
use function is_string;

class DefaultNodeRenderer implements NodeRenderer, NodeRendererFactoryAware
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    public function setNodeRendererFactory(NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function render(Node $node, Environment $environment): string
    {
        $value = $node->getValue();

        if ($value instanceof Node) {
            return $this->nodeRendererFactory->get($value)->render($value, $environment);
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_callable($value)) {
            return ($value)();
        }

        return '';
    }

    public function supports(Node $node): bool
    {
        return true;
    }
}
