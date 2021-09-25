<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\BlockNode;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\ListLine;

final class ListProduction implements Production
{
    /** @var Parser */
    private $lineDataParser;

    /** @var ListNode */
    private $nodeBuffer;

    /** @var ListLine|null */
    private $listLine = null;

    /** @var bool */
    private $listFlow = true;

    /** @var Environment */
    private $environment;

    public function __construct(Parser\LineDataParser $parser, Environment $environment)
    {
        $this->lineDataParser = $parser;
        $this->environment = $environment;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $this->isListLine($documentParser->getDocumentIterator()->current(), $documentParser->isCode);
    }

    public function trigger(DocumentIterator $documentIterator): ?Node
    {
        $this->nodeBuffer = new ListNode();
        $this->parseListLine($documentIterator->current());

        while ($documentIterator->getNextLine() !== null && $this->isListLine($documentIterator->getNextLine(), false)) {
            $documentIterator->next();
            $this->parseListLine($documentIterator->current());
        }

        $this->parseListLine(null, true);

        return $this->nodeBuffer;
    }

    private function isListLine(string $line, bool $isCode): bool
    {
        $listLine = $this->lineDataParser->parseListLine($line);

        if ($listLine !== null) {
            return $listLine->getDepth() === 0 || !$isCode;
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
            } else {
                if ($this->listLine instanceof ListLine && ($this->listFlow || $line[0] === ' ')) {
                    $this->listLine->addText($line);
                } else {
                    $flush = true;
                }
            }

            $this->listFlow = true;
        } else {
            $this->listFlow = false;
        }

        if ($flush) {
            if ($this->listLine instanceof ListLine) {
                $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

                /** @var ListNode $listNode */
                $listNode = $this->nodeBuffer;

                $listNode->addLine($this->listLine->toArray());

                $this->listLine = null;
            }

            return false;
        }

        return true;
    }
}
