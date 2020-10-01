<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\TableNode;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;
use LogicException;
use function sprintf;

class TableNodeRenderer implements NodeRenderer
{
    /** @var TableNode */
    private $tableNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(TableNode $tableNode, TemplateRenderer $templateRenderer)
    {
        $this->tableNode        = $tableNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        $headers = $this->tableNode->getHeaders();
        $rows    = $this->tableNode->getData();

        $tableHeaderRows = [];

        foreach ($headers as $k => $isHeader) {
            if ($isHeader === false) {
                continue;
            }

            if (! isset($rows[$k])) {
                throw new LogicException(sprintf('Row "%d" should be a header, but that row does not exist.', $k));
            }

            $tableHeaderRows[] = $rows[$k];
            unset($rows[$k]);
        }

        return $this->templateRenderer->render('table.html.twig', [
            'tableNode' => $this->tableNode,
            'tableHeaderRows' => $tableHeaderRows,
            'tableRows' => $rows,
        ]);
    }
}
