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

    /** @var bool public is temporary */
    public $isCode = false;

    /** @var DocumentIterator */
    private $documentIterator;

    /** @var string */
    private $state;

    /** @var TitleNode */
    public $lastTitleNode;

    /** @var ArrayObject<int, TitleNode> */
    public $openSectionsAsTitleNodes;

    /** @var Subparsers\Subparser|null */
    private $subparser;

    /** @var array<string, Subparsers\Subparser> */
    private $subparsers;

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
        $this->environment = $parser->getEnvironment();
        $this->eventManager = $eventManager;
        $this->directives = new ArrayObject($directives);
        $this->lineDataParser = new LineDataParser($this->parser, $eventManager);
        $this->lineChecker = new LineChecker($this->lineDataParser);
        $this->tableParser = new TableParser();
        $this->buffer = new Buffer();
        $this->openSectionsAsTitleNodes = new ArrayObject();
        $this->documentIterator = new DocumentIterator();

        $this->subparsers = [
            State::DIRECTIVE => new Subparsers\DirectiveParser(
                $this->parser,
                $this->lineChecker,
                $this->lineDataParser,
                $this->directives
            ),
        ];

        $this->productions = [
            new Productions\TitleRule($this->parser, $this),
            new Productions\LinkRule($this->lineDataParser, $this->environment),
            new Productions\CodeRule(),
            new Productions\QuoteRule($parser),
            new Productions\ListRule($this->lineDataParser, $this->environment),
            // new Productions\CommentProduction(), // Can't use right now, not until Directives are migrated
            new Productions\DefinitionListRule($this->lineDataParser),
        ];
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return true;
    }

    public function apply(DocumentIterator $documentIterator): ?Node
    {
        $line = $this->documentIterator->current();

        switch ($this->state) {
            case State::BEGIN:
                if (trim($line) === '') {
                    return $this->document;
                }

                // NEW STUFF: Based on Recursive Descend Parser theory, we have a list of 'productions' that can result
                // in an AST node.
                foreach ($this->productions as $production) {
                    if ($production->applies($this)) {
                        $newNode = $production->apply($this->documentIterator);
                        if ($newNode !== null) {
                            $this->document->addNode($newNode);
                        }

                        return $this->document;
                    }
                }

                if ($this->lineChecker->isDirective($line)) {
                    // TODO: Why this order? Why is the state set to Directive, the buffer cleared, then a flush
                    //       -with state Directive thus- and only then the new Directive initialised? Is this a
                    //       correct order?
                    $this->setState(State::DIRECTIVE);
                    $this->buffer->clear();
                    $this->flush();

                    $this->subparser = $this->subparsers[$this->state];
                    $this->subparser->reset($line);
                    if ($this->subparser->getDirective() instanceof Directive) {
                        $this->directiveParser = $this->subparser;
                    }

                    return $this->document;
                }

                $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

                if ($separatorLineConfig === null) {
                    $this->setState(State::NORMAL);

                    return $this->document;
                }

                $this->setState(State::TABLE);
                $this->subparser = new Subparsers\TableParser(
                    $this->parser,
                    $this->eventManager,
                    $separatorLineConfig
                );
                $this->subparser->reset($line);

                return $this->document;

            case State::NORMAL:
                if (trim($line) === '') {
                    $this->flush();
                    $this->setState(State::BEGIN);

                    return $this->document;
                }

                $specialLetter = $this->lineChecker->isSpecialLine($line);

                if ($specialLetter !== null) {
                    $this->specialLetter = $specialLetter;

                    $lastLine = $this->buffer->pop();

                    if ($lastLine !== null) {
                        $this->buffer->clear();
                        $this->buffer->push($lastLine);
                        $this->setState(State::TITLE);
                    } else {
                        $this->buffer->push($line);
                        $this->setState(State::SEPARATOR);
                    }

                    $this->flush();
                    $this->setState(State::BEGIN);

                    return $this->document;
                }

                if ($this->lineChecker->isDirective($line)) {
                    $this->flush();
                    $this->setState(State::BEGIN);

                    return null;
                }

                $this->buffer->push($line);

                return $this->document;

            case State::TABLE:
                if (!$this->subparser->parse($line)) {
                    $this->flush();
                    $this->setState(State::BEGIN);
                }

                return $this->document;

            case State::DIRECTIVE:
                $directiveOption = $this->lineDataParser->parseDirectiveOption($line);
                if ($directiveOption !== null && $this->subparser->getDirective() !== null) {
                    $this->subparser->getDirective()->setOption(
                        $directiveOption->getName(),
                        $directiveOption->getValue()
                    );

                    return $this->document;
                }

                $isDirective = $this->lineChecker->isDirective($line);
                if ($isDirective !== false) {
                    // Another new directive has been opened, so let's go back to the begin state and restart parsing
                    $this->setState(State::BEGIN);

                    return null;
                }

                // It's not an option, not a new Directive thus it must be a Content Block!
                $directiveHandler = $this->subparser->getDirectiveHandler();
                $this->isCode = $directiveHandler !== null ? $directiveHandler->wantCode() : false;
                $this->setState(State::BEGIN);

                return null;

            default:
                $this->environment->addError('Parser ended in an unexpected state');
        }

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

    private function setState(string $state): void
    {
        $this->state = $state;
        $this->subparser = null;
    }

    private function parseLines(string $document): void
    {
        $this->lastTitleNode = null;
        $this->specialLetter = false;
        $this->buffer->clear();
        $this->openSectionsAsTitleNodes->exchangeArray([]); // clear it
        $this->setState(State::BEGIN);

        $this->documentIterator->load($this->environment, $document);

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

        // DocumentNode is flushed twice to trigger the directives
        $this->flush();
        $this->flush();

        foreach ($this->openSectionsAsTitleNodes as $titleNode) {
            $this->endOpenSection($titleNode);
        }

        foreach ($this->directives as $directive) {
            $directive->finalize($this->document);
        }
    }

    private function flush(): void
    {
        $node = null;

        $this->isCode = false;

        if ($this->buffer->isEmpty() === false) {
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

                case State::TABLE:
                    $node = $this->subparser->build();

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
