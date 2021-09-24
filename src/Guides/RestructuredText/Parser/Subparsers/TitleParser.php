<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use ArrayObject;
use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

use function array_search;
use function trim;

final class TitleParser implements Subparser
{
    /** @var LineChecker */
    private $lineChecker;

    /** @var Parser\Buffer */
    private $buffer;

    /** @var string */
    private $specialLetter;

    /** @var Environment */
    private $environment;

    /** @var TitleNode|null */
    private $lastTitleNode;

    /** @var DocumentNode */
    private $document;

    /** @var ArrayObject<int, TitleNode> */
    private $openTitleNodes;

    /**
     * @param ArrayObject<int, TitleNode> $openTitleNodes
     */
    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        Parser\Buffer $buffer,
        string $specialLetter,
        ?TitleNode $lastTitleNode,
        DocumentNode $document,
        ArrayObject $openTitleNodes
    ) {
        $this->lineChecker = new LineChecker(new LineDataParser($parser, $eventManager));
        $this->environment = $parser->getEnvironment();
        $this->buffer = $buffer;
        $this->specialLetter = $specialLetter;
        $this->lastTitleNode = $lastTitleNode;
        $this->document = $document;
        $this->openTitleNodes = $openTitleNodes;
    }

    public function reset(string $openingLine): void
    {
    }

    public function parse(string $line): bool
    {
        return $this->lineChecker->isComment($line) || (trim($line) !== '' && $line[0] === ' ');
    }

    /**
     * @return TitleNode|null
     */
    public function build(): ?Node
    {
        $data = $this->buffer->getLinesString();

        $level = $this->environment->getLevel((string) $this->specialLetter);
        $level = $this->environment->getInitialHeaderLevel() + $level - 1;

        $node = new TitleNode(
            new SpanNode($this->environment, $data),
            $level
        );

        if ($this->lastTitleNode !== null) {
            // current level is less than previous so we need to end all open sections
            if ($node->getLevel() < $this->lastTitleNode->getLevel()) {
                foreach ($this->openTitleNodes as $titleNode) {
                    $this->endOpenSection($titleNode);
                }

                // same level as the last so just close the last open section
            } elseif ($node->getLevel() === $this->lastTitleNode->getLevel()) {
                $this->endOpenSection($this->lastTitleNode);
            }
        }

        return $node;
    }

    private function endOpenSection(TitleNode $titleNode): void
    {
        $this->document->addNode(new SectionEndNode($titleNode));

        $key = array_search($titleNode, $this->openTitleNodes->getArrayCopy(), true);

        if ($key === false) {
            return;
        }

        unset($this->openTitleNodes[$key]);
    }
}
