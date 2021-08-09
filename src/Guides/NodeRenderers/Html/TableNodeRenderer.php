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
use LogicException;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\Renderer;

use function sprintf;

class TableNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render(Node $node): string
    {
        if ($node instanceof TableNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $headers = $node->getHeaders();
        $rows = $node->getData();

        $tableHeaderRows = [];

        foreach ($headers as $k => $isHeader) {
            if ($isHeader === false) {
                continue;
            }

            if (!isset($rows[$k])) {
                throw new LogicException(sprintf('Row "%d" should be a header, but that row does not exist.', $k));
            }

            $tableHeaderRows[] = $rows[$k];
            unset($rows[$k]);
        }

        return $this->renderer->render(
            'table.html.twig',
            [
                'tableNode' => $node,
                'tableHeaderRows' => $tableHeaderRows,
                'tableRows' => $rows,
            ]
        );
    }
}
