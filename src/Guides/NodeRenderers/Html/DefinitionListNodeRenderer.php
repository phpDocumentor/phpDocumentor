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

use InvalidArgumentException;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;

class DefinitionListNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render(Node $node): string
    {
        if ($node instanceof DefinitionListNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        return $this->renderer->render(
            'definition-list.html.twig',
            [
                'definitionListNode' => $node,
                'definitionList' => $node->getDefinitionList(),
            ]
        );
    }
}
