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

namespace phpDocumentor\Guides\NodeRenderers\LaTeX;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\MainNode;
use phpDocumentor\Guides\Nodes\Node;

use function count;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function render(Node $node): string
    {
        if ($node instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        return (new BaseDocumentRender($this->environment->getNodeRendererFactory()))->render($node);
    }

    public function renderDocument(DocumentNode $node, Environment $environment): string
    {
        $renderer = $environment->getRenderer();

        return $renderer->render(
            'document.tex.twig',
            [
                'isMain' => $this->isMain($node),
                'document' => $node,
                'body' => $this->render($node),
            ]
        );
    }

    private function isMain(DocumentNode $node): bool
    {
        $nodes = $node->getNodes(
            static function ($node) {
                return $node instanceof MainNode;
            }
        );

        return count($nodes) !== 0;
    }
}
