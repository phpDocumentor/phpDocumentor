<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\ListLine;

use function trim;

final class ListParser implements Subparser
{
    /** @var LineDataParser */
    private $lineDataParser;

    /** @var ListLine|null */
    private $listLine = null;

    /** @var ListNode */
    private $nodeBuffer;

    /** @var Environment */
    private $environment;

    /** @var bool */
    private $listFlow = true;

    public function __construct(Parser $parser, EventManager $eventManager)
    {
        $this->lineDataParser = new LineDataParser($parser, $eventManager);
        $this->environment = $parser->getEnvironment();

        $this->nodeBuffer = new ListNode();
    }

    public function parse(string $line): bool
    {
        return $this->parseListLine($line);
    }

    /**
     * @return ListNode
     */
    public function build(): Node
    {
        $this->parseListLine(null, true);

        return $this->nodeBuffer;
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
