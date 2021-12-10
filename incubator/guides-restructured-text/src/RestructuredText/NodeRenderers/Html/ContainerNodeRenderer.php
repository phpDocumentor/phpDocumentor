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
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\Nodes\ContainerNode;

final class ContainerNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof ContainerNode;
    }

    public function render(Node $node, Environment $environment): string
    {
        if ($node instanceof ContainerNode === false) {
            throw new InvalidArgumentException('Node must be an instance of ' . ContainerNode::class);
        }

        return $this->renderer->render(
            'directives/container.html.twig',
            [
                'class' => $node->getOption('class'),
                'id' => $node->getOption('name'),
                'node' => $node->getValue(),
            ]
        );
    }
}
