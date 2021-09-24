<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

final class DefinitionListParser implements Subparser
{
    /** @var LineDataParser */
    private $lineDataParser;

    /** @var Parser\Buffer */
    private $buffer;

    /** @var LineChecker */
    private $lineChecker;

    /** @var Parser\DocumentIterator */
    private $documentIterator;

    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        Parser\Buffer $buffer,
        Parser\DocumentIterator $documentIterator
    ) {
        $this->lineDataParser = new LineDataParser($parser, $eventManager);
        $this->lineChecker = new LineChecker($this->lineDataParser);
        $this->buffer = $buffer;
        $this->documentIterator = $documentIterator;
    }

    public function reset(string $openingLine): void
    {
    }

    public function parse(string $line): bool
    {
        if ($this->lineChecker->isDefinitionListEnded($line, $this->documentIterator->getNextLine())) {
            return false;
        }

        $this->buffer->push($line);

        return true;
    }

    /**
     * @return DefinitionListNode
     */
    public function build(): ?Node
    {
        $definitionList = $this->lineDataParser->parseDefinitionList($this->buffer->getLines());

        return new DefinitionListNode($definitionList);
    }
}
