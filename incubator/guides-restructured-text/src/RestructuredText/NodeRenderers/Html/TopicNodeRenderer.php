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

namespace phpDocumentor\Guides\RestructuredText\NodeRenderers\Html;

use InvalidArgumentException;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\Nodes\TopicNode;

final class TopicNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof TopicNode;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        if ($node instanceof TopicNode === false) {
            throw new InvalidArgumentException('Node must be an instance of ' . TopicNode::class);
        }

        return $this->renderer->render(
            'directives/topic.html.twig',
            [
                'name' => $node->getName(),
                'node' => $node->getValue(),
            ]
        );
    }
}
