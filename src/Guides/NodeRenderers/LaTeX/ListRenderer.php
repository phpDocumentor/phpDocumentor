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

use phpDocumentor\Guides\NodeRenderers\FormatListRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;
use RuntimeException;

use function array_filter;
use function array_map;
use function array_values;
use function count;
use function explode;

class ListRenderer implements FormatListRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function createElement(Node $node, string $text, string $prefix): string
    {
        return $this->renderer->render(
            'list-item.tex.twig',
            [
                'listNode' => $node,
                'text' => $text,
                'prefix' => $prefix,
            ]
        );
    }

    /**
     * @return string[]
     */
    public function createList(Node $node, bool $ordered): array
    {
        $lines = explode(
            "\n",
            $this->renderer->render(
                'list.tex.twig',
                [
                    'listNode' => $node,
                    'keyword' => $ordered ? 'enumerate' : 'itemize',
                ]
            )
        );

        $lines = array_map('trim', $lines);

        $lines = array_values(
            array_filter(
                $lines,
                static function (string $line) {
                    return $line !== '';
                }
            )
        );

        if (count($lines) !== 2) {
            throw new RuntimeException('list.tex.twig must contain only 2 lines');
        }

        return $lines;
    }
}
