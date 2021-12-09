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

namespace phpDocumentor\Guides\NodeRenderers\Html;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;
use Webmozart\Assert\Assert;

class DefinitionListNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render(Node $node, Environment $environment): string
    {
        Assert::isInstanceOf($node, DefinitionListNode::class);

        return $this->renderer->render(
            'definition-list.html.twig',
            [
                'definitionListNode' => $node,
                'definitionList' => $node->getDefinitionList(),
            ]
        );
    }

    public function supports(Node $node): bool
    {
        return $node instanceof DefinitionListNode;
    }
}
