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

namespace phpDocumentor\Guides\Renderers;

use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use function array_pop;
use function count;

class ListNodeRenderer implements NodeRenderer
{
    /** @var ListNode */
    private $listNode;

    /** @var FormatListRenderer */
    private $formatListRenderer;

    public function __construct(ListNode $listNode, FormatListRenderer $formatListRenderer)
    {
        $this->listNode = $listNode;
        $this->formatListRenderer = $formatListRenderer;
    }

    public function render() : string
    {
        $depth = -1;
        $value = '';
        $stack = [];

        foreach ($this->listNode->getLines() as $line) {
            /** @var SpanNode $text */
            $text = $line['text'];

            $prefix = $line['prefix'];
            $ordered = $line['ordered'];
            $newDepth = $line['depth'];

            if ($depth < $newDepth) {
                $tags = $this->formatListRenderer->createList($ordered);
                $value .= $tags[0];
                $stack[] = [$newDepth, $tags[1] . "\n"];
                $depth = $newDepth;
            }

            while ($depth > $newDepth) {
                $top = $stack[count($stack) - 1];

                if ($top[0] <= $newDepth) {
                    continue;
                }

                $value .= $top[1];
                array_pop($stack);
                $top = $stack[count($stack) - 1];
                $depth = $top[0];
            }

            $value .= $this->formatListRenderer->createElement($text->render(), $prefix) . "\n";
        }

        while ($stack) {
            [, $closing] = array_pop($stack);
            $value .= $closing;
        }

        return $value;
    }
}
