<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use ArrayObject;
use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SeparatorNode;
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

class DocumentParser
{
    /** @var Parser */
    private $parser;

    /** @var Environment */
    private $environment;

    /** @var EventManager */
    private $eventManager;

    /** @var ArrayObject<DirectiveHandler> */
    private $directives;

    /** @var DocumentNode */
    private $document;

    /** @var false|string|null */
    private $specialLetter;

    /** @var Subparsers\DirectiveParser|null */
    private $directiveParser = null;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var LineChecker */
    private $lineChecker;

    /** @var TableParser */
    private $tableParser;

    /** @var Buffer */
    private $buffer;

    /** @var Node|null */
    private $nodeBuffer;

    /** @var bool */
    private $isCode = false;

    /** @var DocumentIterator */
    private $documentIterator;

    /** @var string */
    private $state;

    /** @var TitleNode */
    private $lastTitleNode;

    /** @var ArrayObject<int, TitleNode> */
    private $openTitleNodes;

    /** @var Subparsers\Subparser|null */
    private $subparser;

    /** @var array<string, Subparsers\Subparser> */
    private $subparsers;

    /**
     * @param DirectiveHandler[] $directives
     */
    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        array $directives
    ) {
        $this->parser = $parser;
        $this->environment = $parser->getEnvironment();
        $this->eventManager = $eventManager;
        $this->directives = new ArrayObject($directives);
        $this->lineDataParser = new LineDataParser($this->parser, $eventManager);
        $this->lineChecker = new LineChecker($this->lineDataParser);
        $this->tableParser = new TableParser();
        $this->buffer = new Buffer();
        $this->openTitleNodes = new ArrayObject();
        $this->documentIterator = new DocumentIterator();

        $this->subparsers = [
            State::LIST => new Subparsers\ListParser($this->parser, $this->eventManager),
            State::DEFINITION_LIST => new Subparsers\DefinitionListParser(
                $parser,
                $eventManager,
                $this->buffer,
                $this->documentIterator
            ),
            State::COMMENT => new Subparsers\CommentParser($this->parser, $this->eventManager),
            State::BLOCK => new Subparsers\QuoteParser($this->parser, $this->eventManager, $this->buffer),
            State::CODE => new Subparsers\CodeParser($this->parser, $this->eventManager, $this->buffer),
            State::DIRECTIVE => new Subparsers\DirectiveParser($this->parser, $this->lineChecker, $this->lineDataParser, $this->directives),
        ];
    }

    public function getDocument(): DocumentNode
    {
        return $this->document;
    }

    public function parse(string $contents): DocumentNode
    {
        $preParseDocumentEvent = new PreParseDocumentEvent($this->parser, $contents);

        $this->eventManager->dispatchEvent(
            PreParseDocumentEvent::PRE_PARSE_DOCUMENT,
            $preParseDocumentEvent
        );

        $this->document = new DocumentNode(md5($contents));

        $this->init();

        $this->parseLines(trim($preParseDocumentEvent->getContents()));

        foreach ($this->directives as $directive) {
            $directive->finalize($this->document);
        }

        $this->eventManager->dispatchEvent(
            PostParseDocumentEvent::POST_PARSE_DOCUMENT,
            new PostParseDocumentEvent($this->document)
        );

        return $this->document;
    }

    private function init(): void
    {
        $this->specialLetter = false;
        $this->buffer->clear();
        $this->nodeBuffer = null;
    }

    private function setState(string $state, string $openingLine): void
    {
        $this->state = $state;
        $this->subparser = null;

        switch ($state) {
            case State::TITLE:
                // The amount of state being passed to the TitleParser is questionable. But to keep it simple for now,
                // we keep it like this.
                $this->subparser = new Subparsers\TitleParser(
                    $this->parser,
                    $this->eventManager,
                    $this->buffer,
                    $this->specialLetter,
                    $this->lastTitleNode,
                    $this->document,
                    $this->openTitleNodes
                );
                $this->subparser->reset($openingLine);
                break;
            case State::LIST:
            case State::DEFINITION_LIST:
            case State::COMMENT:
            case State::BLOCK:
            case State::CODE:
                $subparser = $this->subparsers[$state] ?? null;
                if ($subparser !== null) {
                    $this->subparser = $subparser;
                    $this->subparser->reset($openingLine);
                }
                break;
        }
    }

    private function parseLines(string $document): void
    {
        $this->documentIterator->load($this->environment, $document);
        $this->setState(State::BEGIN, '');

        foreach ($this->documentIterator as $line) {
            while (true) {
                if ($this->parseLine($line)) {
                    break;
                }
            }
        }

        // DocumentNode is flushed twice to trigger the directives
        $this->flush();
        $this->flush();

        foreach ($this->openTitleNodes as $titleNode) {
            $this->endOpenSection($titleNode);
        }
    }

    private function parseLine(string $line): bool
    {
        switch ($this->state) {
            case State::BEGIN:
                if (trim($line) !== '') {
                    if ($this->lineChecker->isListLine($line, $this->isCode)) {
                        $this->setState(State::LIST, $line);

                        return false;
                    }

                    if ($this->lineChecker->isBlockLine($line)) {
                        if ($this->isCode) {
                            $this->setState(State::CODE, $line);
                        } else {
                            $this->setState(State::BLOCK, $line);
                        }

                        return false;
                    }

                    if ($this->parseLink($line)) {
                        return true;
                    }

                    if ($this->lineChecker->isDirective($line)) {
                        // TODO: Why this order? Why is the state set to Directive, the buffer cleared, then a flush
                        //       -with state Directive thus- and only then the new Directive initialised? Is this a
                        //       correct order?
                        $this->setState(State::DIRECTIVE, $line);
                        $this->buffer->clear();
                        $this->flush();
                        $this->subparser = $this->subparsers[$this->state];
                        $this->subparser->reset($line);
                        if ($this->subparser->getDirective() instanceof Directive) {
                            $this->directiveParser = $this->subparser;
                        }
                    } elseif ($this->lineChecker->isDefinitionList($this->documentIterator->getNextLine())) {
                        $this->setState(State::DEFINITION_LIST, $line);
                        $this->buffer->push($line);

                        return true;
                    } else {
                        $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

                        if ($separatorLineConfig === null) {
                            $this->setState(State::NORMAL, $line);

                            return false;
                        }

                        $this->setState(State::TABLE, $line);
                        $this->subparser = new Subparsers\TableParser(
                            $this->parser,
                            $this->eventManager,
                            $separatorLineConfig
                        );
                        $this->subparser->reset($line);
                    }
                }

                break;

            case State::NORMAL:
                if (trim($line) !== '') {
                    $specialLetter = $this->lineChecker->isSpecialLine($line);

                    if ($specialLetter !== null) {
                        $this->specialLetter = $specialLetter;

                        $lastLine = $this->buffer->pop();

                        if ($lastLine !== null) {
                            $this->buffer->clear();
                            $this->buffer->push($lastLine);
                            $this->setState(State::TITLE, $line);
                        } else {
                            $this->buffer->push($line);
                            $this->setState(State::SEPARATOR, $line);
                        }

                        $this->flush();
                        $this->setState(State::BEGIN, $line);
                    } elseif ($this->lineChecker->isDirective($line)) {
                        $this->flush();
                        $this->setState(State::BEGIN, $line);

                        return false;
                    } elseif ($this->lineChecker->isComment($line)) {
                        $this->flush();
                        $this->setState(State::COMMENT, $line);
                    } else {
                        $this->buffer->push($line);
                    }
                } else {
                    $this->flush();
                    $this->setState(State::BEGIN, $line);
                }

                break;

            case State::BLOCK:
            case State::CODE:
            case State::LIST:
            case State::DEFINITION_LIST:
                if ($this->subparser->parse($line) === false) {
                    $this->flush();
                    $this->setState(State::BEGIN, $line);

                    return false;
                }

                break;

            case State::TABLE:
                if (!$this->subparser->parse($line)) {
                    $this->flush();
                    $this->setState(State::BEGIN, $line);

                    // TODO: No return?
                }

                break;

            case State::COMMENT:
                if (!$this->subparser->parse($line)) {
                    // No flush, a Comment is an inline element and should not interrupt parsing this structural element
                    $this->setState(State::BEGIN, $line);

                    return false;
                }

                break;

            case State::DIRECTIVE:
                $directiveOption = $this->lineDataParser->parseDirectiveOption($line);
                if ($directiveOption !== null && $this->subparser->getDirective() !== null) {
                    $this->subparser->getDirective()->setOption(
                        $directiveOption->getName(),
                        $directiveOption->getValue()
                    );
                    return true;
                }

                if ($this->subparser->parse($line) === false) {
                    $directiveHandler = $this->subparser->getDirectiveHandler();
                    $this->isCode = $directiveHandler !== null ? $directiveHandler->wantCode() : false;
                    $this->setState(State::BEGIN, $line);

                    return false;
                }

                $this->flush();

                // Initiate a new Directive if the Linechecker indicated that the line is a new one
                $this->subparser = $this->subparsers[State::DIRECTIVE];
                $this->subparser->reset($line);
                if ($this->subparser->getDirective() instanceof Directive) {
                    $this->directiveParser = $this->subparser;
                }

                break;

            default:
                $this->environment->addError('Parser ended in an unexpected state');
        }

        return true;
    }

    private function flush(): void
    {
        $node = null;

        $this->isCode = false;

        if ($this->hasBuffer()) {
            switch ($this->state) {
                case State::NORMAL:
                    $this->isCode = $this->prepareCode();

                    $node = new ParagraphNode(new SpanNode($this->environment, $this->buffer->getLinesString()));

                    break;
                case State::SEPARATOR:
                    // TODO: Move this to the subparsers property, but how to propagate the specialLetter?
                    $this->subparser = new Parser\Subparsers\SeparatorParser($this->parser, $this->specialLetter);
                    $node = $this->subparser->build();
                    break;

                case State::CODE:
                case State::BLOCK:
                case State::LIST:
                case State::DEFINITION_LIST:
                case State::TABLE:
                case State::COMMENT:
                    $node = $this->subparser->build();

                    break;
                case State::TITLE:
                    $node = $this->subparser->build();
                    if ($node instanceof TitleNode === false) {
                        throw new RuntimeException('Expected a TitleNode');
                    }

                    $this->lastTitleNode = $node;
                    $this->document->addNode(new SectionBeginNode($node));
                    $this->openTitleNodes->append($node);

                    break;
            }
        }

        if ($this->directiveParser !== null) {
            $this->directiveParser->setContentBlock($node);
            $this->directiveParser->build();
            if ($node instanceof CodeNode) {
                $node = null;
            }
        }

        $this->directiveParser = null;

        if ($node !== null) {
            $this->document->addNode($node);
        }

        $this->init();
    }

    private function hasBuffer(): bool
    {
        return !$this->buffer->isEmpty() || $this->nodeBuffer !== null;
    }

    private function prepareCode(): bool
    {
        $lastLine = $this->buffer->getLastLine();

        if ($lastLine === null) {
            return false;
        }

        $trimmedLastLine = trim($lastLine);

        if (strlen($trimmedLastLine) >= 2) {
            if (substr($trimmedLastLine, -2) === '::') {
                if (trim($trimmedLastLine) === '::') {
                    $this->buffer->pop();
                } else {
                    $this->buffer->set($this->buffer->count() - 1, substr($trimmedLastLine, 0, -1));
                }

                return true;
            }
        }

        return false;
    }

    private function parseLink(string $line): bool
    {
        $link = $this->lineDataParser->parseLink($line);

        if ($link === null) {
            return false;
        }

        if ($link->getType() === Link::TYPE_ANCHOR) {
            $anchorNode = new AnchorNode($link->getName());

            $this->document->addNode($anchorNode);
        }

        $this->environment->setLink($link->getName(), $link->getUrl());

        return true;
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
