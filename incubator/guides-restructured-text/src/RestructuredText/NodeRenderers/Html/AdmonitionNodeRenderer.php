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
use phpDocumentor\Guides\RestructuredText\Nodes\AdmonitionNode;

class AdmonitionNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof AdmonitionNode;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        if ($node instanceof AdmonitionNode === false) {
            throw new InvalidArgumentException('Node must be an instance of ' . AdmonitionNode::class);
        }

        return $this->renderer->render(
            'directives/admonition.html.twig',
            [
                'name' => $node->getName(),
                'text' => $node->getText(),
                'class' => $node->getOption('class'),
                'node' => $node->getValue(),
            ]
        );
    }
}
