<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class ParagraphState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var DocumentParser */
    private $documentParser;

    /** @var LineChecker */
    private $lineChecker;

    /** @var Environment */
    private $environment;

    public function __construct(DocumentParser $documentParser, LineChecker $lineChecker, Environment $environment)
    {
        $this->documentParser = $documentParser;
        $this->lineChecker = $lineChecker;
        $this->environment = $environment;
    }

    public function getName() : string
    {
        return StateName::PARAGRAPH;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;
    }

    public function parse(string $line) : bool
    {
        if (trim($line) === '') {
            $this->documentParser->transitionTo(StateName::BEGIN, [], true);

            return true;
        }

        $specialLetter = $this->lineChecker->isSpecialLine($line);

        if ($specialLetter !== null) {
            $this->documentParser->setSpecialLetter($specialLetter);

            $lastLine = $this->buffer->pop();

            if ($lastLine !== null) {
                $this->buffer->clear();
                $this->buffer->push($lastLine);
                $this->documentParser->transitionTo(StateName::TITLE);
            } else {
                $this->buffer->push($line);
                $this->documentParser->transitionTo(StateName::SEPARATOR);
            }

            $this->documentParser->transitionTo(StateName::BEGIN, [], true);

            return true;
        }

        if ($this->lineChecker->isDirective($line)) {
            return false;
        }

        if ($this->lineChecker->isComment($line)) {
            $this->documentParser->transitionTo(StateName::COMMENT, [], true);

            return true;
        }

        $this->buffer->push($line);

        return true;
    }

    public function leave() : ?Node
    {
        $buffer = $this->buffer->getLinesString();

        return new ParagraphNode(new SpanNode($this->environment, $buffer));
    }
}
