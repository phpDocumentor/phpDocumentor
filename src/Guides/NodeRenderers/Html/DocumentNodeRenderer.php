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
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->renderer = $this->environment->getRenderer();
    }

    public function render(Node $node) : string
    {
        if ($node instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        return (new BaseDocumentRender($this->environment->getNodeRendererFactory()))->render($node);
    }

    public function renderDocument(DocumentNode $node) : string
    {
        $this->renderer->setGuidesEnvironment($this->environment);
        $this->renderer->setDestination($this->environment->getUrl());

        return $this->renderer->render(
            'document.html.twig',
            ['node' => $node]
        );
    }
}
