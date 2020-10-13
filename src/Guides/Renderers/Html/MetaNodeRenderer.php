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

use phpDocumentor\Guides\Nodes\MetaNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class MetaNodeRenderer implements NodeRenderer
{
    /** @var MetaNode */
    private $metaNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(MetaNode $metaNode)
    {
        $this->metaNode = $metaNode;
        $this->renderer = $metaNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render(
            'meta.html.twig',
            [
                'metaNode' => $this->metaNode,
            ]
        );
    }
}
