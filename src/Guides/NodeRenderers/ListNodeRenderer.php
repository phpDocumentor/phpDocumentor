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
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;

use function array_pop;
use function count;
use function get_class;

class ListNodeRenderer implements NodeRenderer
{
    /** @var FormatListRenderer */
    private $formatListRenderer;

    /** @var Environment */
    private $environment;

    public function __construct(FormatListRenderer $formatListRenderer, Environment $environment)
    {
        $this->formatListRenderer = $formatListRenderer;
        $this->environment = $environment;
    }

    public function render(Node $node): string
    {
        if ($node instanceof ListNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $depth = -1;
        $value = '';
        $stack = [];

        foreach ($node->getLines() as $line) {
            /** @var SpanNode $text */
            $text = $line['text'];

            $prefix = $line['prefix'];
            $ordered = $line['ordered'];
            $newDepth = $line['depth'];

            if ($depth < $newDepth) {
                $tags = $this->formatListRenderer->createList($node, $ordered);
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

            $renderedText = $this->environment->getNodeRendererFactory()->get(get_class($text))->render($text);
            $value .= $this->formatListRenderer->createElement($node, $renderedText, $prefix) . "\n";
        }

        while ($stack) {
            [, $closing] = array_pop($stack);
            $value .= $closing;
        }

        return $value;
    }
}
