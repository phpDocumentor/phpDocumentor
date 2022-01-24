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
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;
use phpDocumentor\Guides\RestructuredText\Parser\TableParser;

use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#tables
 */
final class TableRule implements Rule
{
    /** @var MarkupLanguageParser */
    private $parser;

    /** @var LineChecker */
    private $lineChecker;

    /** @var TableParser */
    private $tableParser;

    public function __construct(MarkupLanguageParser $parser)
    {
        $this->parser = $parser;
        $this->lineChecker = new LineChecker(new LineDataParser($parser));
        $this->tableParser = new TableParser();
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $this->tableParser->parseTableSeparatorLine($documentParser->getDocumentIterator()->current()) !== null;
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $line = $documentIterator->current();
        if (trim($line) === '') {
            return null;
        }

        $tableSeparatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);
        $node = new TableNode($tableSeparatorLineConfig, $this->tableParser->guessTableType($line));
        $node->pushSeparatorLine($tableSeparatorLineConfig);

        while ($documentIterator->getNextLine() !== null) {
            $documentIterator->next();
            $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($documentIterator->current());
            if ($separatorLineConfig !== null) {
                $node->pushSeparatorLine($separatorLineConfig);
                // if an empty line follows a separator line, then it is the end of the table
                if ($documentIterator->getNextLine() === null || trim($documentIterator->getNextLine()) === '') {
                    break;
                }

                continue;
            }

            $node->pushContentLine($documentIterator->current());
        }

        $node->finalize($this->parser, $this->lineChecker);

        return $node;
    }
}
