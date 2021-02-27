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

use Highlight\Highlighter;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use function array_keys;
use function array_merge;
use function array_reverse;
use function count;
use function implode;
use function in_array;
use function preg_split;
use function rtrim;
use function str_pad;
use function trim;
use const STR_PAD_LEFT;

class CodeNodeRenderer implements NodeRenderer
{
    /** @var CodeNode */
    private $codeNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(CodeNode $codeNode)
    {
        $this->codeNode = $codeNode;
        $this->renderer = $codeNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        $value = $this->codeNode->getValue();

        if ($this->codeNode->isRaw()) {
            return $value;
        }

        return $this->renderer->render(
            'code.html.twig',
            [
                'code' => $this->codeNode->getValue(),
                'language' => $this->codeNode->getLanguage(),
                'startingLineNumber' => $this->codeNode->getStartingLineNumber(),
            ]
        );
    }
}
