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

namespace phpDocumentor\Guides\Renderers\LaTeX;

use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\FormatListRenderer;
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

    /** @var Renderer */
    private $renderer;

    public function __construct(ListNode $listNode)
    {
        $this->listNode = $listNode;
        $this->renderer = $listNode->getEnvironment()->getRenderer();
    }

    public function createElement(string $text, string $prefix) : string
    {
        return $this->renderer->render(
            'list-item.tex.twig',
            [
                'listNode' => $this->listNode,
                'text' => $text,
                'prefix' => $prefix,
            ]
        );
    }

    /**
     * @return string[]
     */
    public function createList(bool $ordered) : array
    {
        $lines = explode(
            "\n",
            $this->renderer->render(
                'list.tex.twig',
                [
                    'listNode' => $this->listNode,
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
