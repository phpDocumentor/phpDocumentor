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

use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

use function strpos;
use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#definition-lists
 */
final class DefinitionListRule implements Rule
{
    /** @var LineDataParser */
    private $lineDataParser;

    public function __construct(LineDataParser $parser)
    {
        $this->lineDataParser = $parser;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $this->isDefinitionList($documentParser->getDocumentIterator()->getNextLine() ?? '');
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $buffer = new Buffer();

        while (
            $documentIterator->getNextLine() !== null
            && $this->isDefinitionListEnded($documentIterator->current(), $documentIterator->getNextLine()) === false
        ) {
            $buffer->push($documentIterator->current());
            $documentIterator->next();
        }

        // TODO: This is a workaround because the current Main Loop in {@see DocumentParser::parseLines()} expects
        //       the cursor position to rest at the last unprocessed line, but the logic above needs is always a step
        //       'too late' in detecting whether it should have stopped
        $documentIterator->prev();

        $definitionList = $this->lineDataParser->parseDefinitionList($buffer->getLines());

        return new DefinitionListNode($definitionList);
    }

    public function isDefinitionList(string $line): bool
    {
        return strpos($line, '    ') === 0;
    }

    public function isDefinitionListEnded(string $line, string $nextLine): bool
    {
        if (trim($line) === '') {
            return false;
        }

        if ($this->isDefinitionList($line)) {
            return false;
        }

        return !$this->isDefinitionList($nextLine);
    }
}
