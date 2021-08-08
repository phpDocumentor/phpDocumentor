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

namespace phpDocumentor\Guides\NodeRenderers;

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;

use function get_class;

class DocumentNodeRenderer implements NodeRenderer
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    public function __construct(NodeRendererFactory $nodeRendererFactory)
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function render(Node $node): string
    {
        if ($node instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $document = '';

        foreach ($node->getNodes() as $childNode) {
            $renderedNode = $this->nodeRendererFactory->get(get_class($childNode))->render($childNode);
            $document .= $renderedNode . "\n";
        }

        return $document;
    }
}
