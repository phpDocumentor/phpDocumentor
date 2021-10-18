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

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use InvalidArgumentException;
use phpDocumentor\Guides\MarkupLanguageParser;
use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Nodes\Links\Link as LinkParser;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#hyperlink-targets
 */
final class LinkRule implements Rule
{
    /** @var LineDataParser */
    private $lineDataParser;

    /** @var MarkupLanguageParser */
    private $parser;

    public function __construct(LineDataParser $lineDataParser, MarkupLanguageParser $parser)
    {
        $this->lineDataParser = $lineDataParser;
        $this->parser = $parser;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        $link = $this->lineDataParser->parseLink($documentParser->getDocumentIterator()->current());

        return $link !== null;
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $link = $this->lineDataParser->parseLink($documentIterator->current());
        if ($link === null) {
            throw new InvalidArgumentException();
        }

        $node = null;
        if ($link->getType() === LinkParser::TYPE_ANCHOR) {
            $node = new AnchorNode($link->getName());
        }

        $this->parser->getEnvironment()->setLink($link->getName(), $link->getUrl());

        return $node;
    }
}
