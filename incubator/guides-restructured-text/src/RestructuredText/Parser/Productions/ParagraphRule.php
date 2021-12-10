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

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

use function array_pop;
use function implode;
use function substr;
use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#paragraphs
 */
final class ParagraphRule implements Rule
{
    /** @var MarkupLanguageParser */
    private $parser;

    /** @var DocumentParser */
    private $documentParser;

    public function __construct(MarkupLanguageParser $parser, DocumentParser $documentParser)
    {
        $this->parser = $parser;
        $this->documentParser = $documentParser;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        // Should be last in the series of rules; basically: if it ain't anything else, it is a paragraph.
        // This could prove to be wrong when we pull up the spec, but the existing implementation applies this concept
        // and we roll with it for now.
        return trim($documentParser->getDocumentIterator()->current()) !== '';
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $buffer = new Buffer();
        $buffer->push($documentIterator->current());

        while (
            $documentIterator->getNextLine() !== null
            && $this->isWhiteline($documentIterator->getNextLine()) === false
        ) {
            $documentIterator->next();
            $buffer->push($documentIterator->current());
        }

        $lines = $buffer->getLines();
        $lastLine = trim(array_pop($lines) ?? '');

        // https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#literal-blocks
        // 2 colons at the end means that the next Indented Block should be a LiteralBlock and we should remove the
        // colons
        if (substr($lastLine, -2) === '::') {
            $lastLine = trim(substr($lastLine, 0, -2));

            // However, if a line ended in a double colon, we keep one colon
            if ($lastLine !== '' && substr($lastLine, -1) !== ':') {
                $lastLine .= ':';
            }

            $this->documentParser->nextIndentedBlockShouldBeALiteralBlock = true;

            if ($lastLine !== '') {
                $lines[] = $lastLine;
            }
        } else {
            $lines[] = $lastLine;
        }

        if (trim(implode('', $lines)) === '') {
            return null;
        }

        return new ParagraphNode(SpanNode::create($this->parser, $lines));
    }

    private function isWhiteline(string $line): bool
    {
        return trim($line) === '';
    }
}
