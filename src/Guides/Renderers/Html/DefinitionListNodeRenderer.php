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

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class DefinitionListNodeRenderer implements NodeRenderer
{
    /** @var DefinitionListNode */
    private $definitionListNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(DefinitionListNode $definitionListNode)
    {
        $this->definitionListNode = $definitionListNode;
        $this->renderer = $definitionListNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render(
            'definition-list.html.twig',
            [
                'definitionListNode' => $this->definitionListNode,
                'definitionList' => $this->definitionListNode->getDefinitionList(),
            ]
        );
    }
}
