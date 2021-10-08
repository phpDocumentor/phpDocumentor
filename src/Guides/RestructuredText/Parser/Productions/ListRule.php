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

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;
use phpDocumentor\Guides\RestructuredText\Parser\ListLine;

use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#bullet-lists
 */
final class ListRule implements Rule
{
    /** @var LineDataParser */
    private $lineDataParser;

    /** @var ListNode */
    private $nodeBuffer;

    /** @var ListLine|null */
    private $listLine = null;

    /** @var bool */
    private $listFlow = true;

    /** @var Environment */
    private $environment;

    public function __construct(LineDataParser $parser, Environment $environment)
    {
        $this->lineDataParser = $parser;
        $this->environment = $environment;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $this->isListLine($documentParser->getDocumentIterator()->current());
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $this->listLine = null;
        $this->listFlow = true;

        $this->nodeBuffer = new ListNode();
        $this->parseListLine($documentIterator->current());

        while (
            $documentIterator->getNextLine() !== null
            && $this->parseListLine($documentIterator->getNextLine())
        ) {
            $documentIterator->next();
        }

        $this->parseListLine(null, true);

        return $this->nodeBuffer;
    }

    private function isListLine(string $line): bool
    {
        $listLine = $this->lineDataParser->parseListLine($line);

        if ($listLine !== null) {
            return $listLine->getDepth() === 0;
        }

        return false;
    }

    private function parseListLine(?string $line, bool $flush = false): bool
    {
        if ($line !== null && trim($line) !== '') {
            $listLine = $this->lineDataParser->parseListLine($line);

            if ($listLine !== null) {
                if ($this->listLine instanceof ListLine) {
                    $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

                    $this->nodeBuffer->addLine($this->listLine->toArray());
                }

                $this->listLine = $listLine;
            } elseif ($this->listLine instanceof ListLine && ($this->listFlow || $line[0] === ' ')) {
                $this->listLine->addText($line);
            } else {
                $flush = true;
            }

            $this->listFlow = true;
        } else {
            $this->listFlow = false;
        }

        if (!$flush) {
            return true;
        }

        if ($this->listLine instanceof ListLine) {
            $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

            $this->nodeBuffer->addLine($this->listLine->toArray());

            $this->listLine = null;
        }

        return false;
    }
}
