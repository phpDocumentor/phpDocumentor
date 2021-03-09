<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\Lines;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class TitleState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var Environment */
    private $environment;

    /** @var DocumentParser */
    private $documentParser;

    public function __construct(Environment $environment, DocumentParser $documentParser)
    {
        $this->environment = $environment;
        $this->documentParser = $documentParser;
    }

    public function getName() : string
    {
        return StateName::TITLE;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;
    }

    public function parse(string $line) : bool
    {
        return false;
    }

    public function leave() : ?Node
    {
        $data = $this->buffer->getLinesString();

        $letter = $this->documentParser->getSpecialLetter();
        $level = $this->environment->getLevel($letter);
        $level = $this->environment->getInitialHeaderLevel() + $level - 1;

        return new TitleNode(
            new SpanNode($this->environment, $data),
            $level
        );
    }
}
