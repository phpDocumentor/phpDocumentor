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

use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class SectionBeginNodeRenderer implements NodeRenderer
{
    /** @var SectionBeginNode */
    private $sectionBeginNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(SectionBeginNode $sectionBeginNode)
    {
        $this->sectionBeginNode = $sectionBeginNode;
        $this->renderer = $sectionBeginNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render(
            'section-begin.html.twig',
            [
                'sectionBeginNode' => $this->sectionBeginNode,
            ]
        );
    }
}
