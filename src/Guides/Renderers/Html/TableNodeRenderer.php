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

use LogicException;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use function sprintf;

class TableNodeRenderer implements NodeRenderer
{
    /** @var TableNode */
    private $tableNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(TableNode $tableNode)
    {
        $this->tableNode = $tableNode;
        $this->renderer = $tableNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        $headers = $this->tableNode->getHeaders();
        $rows = $this->tableNode->getData();

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
                'tableNode' => $this->tableNode,
                'tableHeaderRows' => $tableHeaderRows,
                'tableRows' => $rows,
            ]
        );
    }
}
