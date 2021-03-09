<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\Lines;
use phpDocumentor\Guides\RestructuredText\Parser\ListLine;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class ListState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var ?ListLine */
    private $listLine;
    /**
     * @var Environment
     */
    private $environment;

    /** @var bool */
    private $listFlow = false;

    /** @var ListNode */
    private $nodeBuffer;

    public function __construct(LineDataParser $lineDataParser, Environment $environment)
    {
        $this->lineDataParser = $lineDataParser;
        $this->environment = $environment;
    }

    public function getName() : string
    {
        return StateName::LIST;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;

        $this->nodeBuffer = new ListNode();

        $this->listLine = null;
        $this->listFlow = true;
    }

    public function parse(string $line) : bool
    {
        return $this->parseListLine($line);
    }

    public function leave() : ?Node
    {
        $this->parseListLine(null, true);

        return $this->nodeBuffer;
    }

    private function parseListLine(?string $line, bool $flush = false) : bool
    {
        if ($line !== null && trim($line) !== '') {
            $listLine = $this->lineDataParser->parseListLine($line);

            if ($listLine !== null) {
                if ($this->listLine instanceof ListLine) {
                    $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

                    $this->nodeBuffer->addLine($this->listLine->toArray());
                }

                $this->listLine = $listLine;
            } else if ($this->listLine instanceof ListLine && ($this->listFlow || $line[0] === ' ')) {
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

        if (!($this->listLine instanceof ListLine)) {
            return false;
        }

        $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

        $this->nodeBuffer->addLine($this->listLine->toArray());

        $this->listLine = null;

        return false;
    }
}
