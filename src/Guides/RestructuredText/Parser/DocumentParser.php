<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use ArrayObject;
use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\Event\PostParseDocumentEvent;
use phpDocumentor\Guides\RestructuredText\Event\PreParseDocumentEvent;
use phpDocumentor\Guides\RestructuredText\Parser;
use RuntimeException;
use function array_search;
use function md5;
use function strlen;
use function substr;
use function trim;

class DocumentParser implements Productions\Rule
{
    /** @var Parser */
    private $parser;

    /** @var EventManager */
    private $eventManager;

    /** @var ArrayObject<DirectiveHandler> */
    private $directives;

    /** @var DocumentNode */
    private $document;

    /** @var bool public is temporary */
    public $nextIndentedBlockShouldBeALiteralBlock = false;

    /** @var DocumentIterator */
    private $documentIterator;

    /** @var TitleNode */
    public $lastTitleNode;

    /** @var ArrayObject<int, TitleNode> */
    public $openSectionsAsTitleNodes;

    /** @var array<int, Productions\Rule> */
    private $productions;

    /**
     * @param DirectiveHandler[] $directives
     */
    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        array $directives
    ) {
        $this->parser = $parser;
        $this->eventManager = $eventManager;

        $this->documentIterator = new DocumentIterator();
        $this->openSectionsAsTitleNodes = new ArrayObject();
        $this->directives = new ArrayObject($directives);

        $lineDataParser = new LineDataParser($this->parser, $eventManager);

        $literalBlockRule = new Productions\LiteralBlockRule();
        $this->productions = [
            new Productions\TitleRule($this->parser, $this),
            new Productions\LinkRule($lineDataParser, $parser->getEnvironment()),
            $literalBlockRule,
            new Productions\QuoteRule($parser),
            new Productions\ListRule($lineDataParser, $parser->getEnvironment()),
            new Productions\DirectiveRule($parser, $this, $lineDataParser, $literalBlockRule, $directives),
            new Productions\CommentRule(),
            new Productions\DefinitionListRule($lineDataParser),

            // For now: ParagraphRule must be last as it is the rule that applies if none other applies.
            new Productions\ParagraphRule($this->parser, $this),
        ];
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return true;
    }

    public function apply(DocumentIterator $documentIterator): ?Node
    {
        foreach ($this->productions as $production) {
            if (!$production->applies($this)) {
                continue;
            }

            $newNode = $production->apply($this->documentIterator);
            if ($newNode !== null) {
                $this->document->addNode($newNode);
            }
            break;
        }

        return $this->document;

//                $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);
//
//                if ($separatorLineConfig === null) {
//                    $this->setState(State::NORMAL);
//
//                    return $this->document;
//                }
//
//                $this->setState(State::TABLE);
//                $this->subparser = new Subparsers\TableParser(
//                    $this->parser,
//                    $this->eventManager,
//                    $separatorLineConfig
//                );
//                $this->subparser->reset($line);
//
//                return $this->document;

//            case State::DIRECTIVE:
//                $directiveOption = $this->lineDataParser->parseDirectiveOption($line);
//                if ($directiveOption !== null && $this->subparser->getDirective() !== null) {
//                    $this->subparser->getDirective()->setOption(
//                        $directiveOption->getName(),
//                        $directiveOption->getValue()
//                    );
//
//                    return $this->document;
//                }
//
//                $isDirective = $this->lineChecker->isDirective($line);
//                if ($isDirective !== false) {
//                    // Another new directive has been opened, so let's go back to the begin state and restart parsing
//                    $this->setState(State::BEGIN);
//
//                    return null;
//                }
//
//                // It's not an option, not a new Directive thus it must be a Content Block!
//                $directiveHandler = $this->subparser->getDirectiveHandler();
//                $this->isCode = $directiveHandler !== null ? $directiveHandler->wantCode() : false;
//                $this->setState(State::BEGIN);
//
//                return null;
    }

    public function parse(string $contents): DocumentNode
    {
        $preParseDocumentEvent = new PreParseDocumentEvent($this->parser, $contents);

        $this->eventManager->dispatchEvent(
            PreParseDocumentEvent::PRE_PARSE_DOCUMENT,
            $preParseDocumentEvent
        );

        $this->document = new DocumentNode(md5($contents));
        $this->parseLines(trim($preParseDocumentEvent->getContents()));

        $this->eventManager->dispatchEvent(
            PostParseDocumentEvent::POST_PARSE_DOCUMENT,
            new PostParseDocumentEvent($this->document)
        );

        return $this->document;
    }

    public function getDocument(): DocumentNode
    {
        return $this->document;
    }

    private function parseLines(string $document): void
    {
        $this->lastTitleNode = null;
        $this->openSectionsAsTitleNodes->exchangeArray([]); // clear it

        $this->documentIterator->load($this->parser->getEnvironment(), $document);

        // We explicitly do not use foreach, but rather the cursors of the DocumentIterator
        // this is done because we are transitioning to a method where a Substate can take the current
        // cursor as starting point and loop through the cursor
        while ($this->documentIterator->valid()) {
            // Continuously attempt to apply the current cursor of the documentIterator until a Node is returned
            // In this loop, we do not do anything with the returned node as it is handled in the apply method itself
            // for now.
            // Be aware, that the production rules called within the apply function may further the cursor of the
            // document iterator. Each production rule is responsible for furthering the parsing process until it is
            // done.
            while ($this->apply($this->documentIterator) === null) {
            }

            $this->documentIterator->next();
        }

        foreach ($this->openSectionsAsTitleNodes as $titleNode) {
            $this->endOpenSection($titleNode);
        }

        foreach ($this->directives as $directive) {
            $directive->finalize($this->document);
        }
    }

    public function endOpenSection(TitleNode $titleNode): void
    {
        $this->document->addNode(new SectionEndNode($titleNode));

        $key = array_search($titleNode, $this->openSectionsAsTitleNodes->getArrayCopy(), true);

        if ($key === false) {
            return;
        }

        unset($this->openSectionsAsTitleNodes[$key]);
    }

    public function getDocumentIterator(): DocumentIterator
    {
        return $this->documentIterator;
    }
}
