<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\ListNode;
use phpDocumentor\Guides\RestructuredText\Renderers\FormatListRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;
use RuntimeException;
use function array_filter;
use function array_map;
use function array_values;
use function count;
use function explode;

class ListRenderer implements FormatListRenderer
{
    /** @var ListNode */
    private $listNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(ListNode $listNode, TemplateRenderer $templateRenderer)
    {
        $this->listNode         = $listNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function createElement(string $text, string $prefix) : string
    {
        return $this->templateRenderer->render('list-item.tex.twig', [
            'listNode' => $this->listNode,
            'text' => $text,
            'prefix' => $prefix,
        ]);
    }

    /**
     * @return string[]
     */
    public function createList(bool $ordered) : array
    {
        $lines = explode("\n", $this->templateRenderer->render('list.tex.twig', [
            'listNode' => $this->listNode,
            'keyword' => $ordered ? 'enumerate' : 'itemize',
        ]));

        $lines = array_map('trim', $lines);

        $lines = array_values(array_filter($lines, static function (string $line) {
            return $line !== '';
        }));

        if (count($lines) !== 2) {
            throw new RuntimeException('list.tex.twig must contain only 2 lines');
        }

        return $lines;
    }
}
