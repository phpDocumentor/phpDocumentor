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

class InMemoryNodeRendererFactory implements NodeRendererFactory
{
    /** @var array<class-string, NodeRenderer> */
    private $nodeRenderers;

    /** @var NodeRenderer */
    private $defaultNodeRenderer;

    /**
     * @param array<class-string, NodeRenderer> $nodeRenderers
     */
    public function __construct(array $nodeRenderers, NodeRenderer $defaultNodeRenderer)
    {
        $this->nodeRenderers = $nodeRenderers;
        foreach ($nodeRenderers as $nodeRenderer) {
            if (!$nodeRenderer instanceof NodeRendererFactoryAware) {
                continue;
            }

            $nodeRenderer->setNodeRendererFactory($this);
        }

        $this->defaultNodeRenderer = $defaultNodeRenderer;
        if (!$defaultNodeRenderer instanceof NodeRendererFactoryAware) {
            return;
        }

        $defaultNodeRenderer->setNodeRendererFactory($this);
    }

    public function get(string $node): NodeRenderer
    {
        return $this->nodeRenderers[$node] ?? $this->defaultNodeRenderer;
    }
}
