<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Directive as ParserDirective;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;
use function array_search;
use function chr;
use function explode;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;
use function trim;

class DocumentParser
{
    /** @var Parser */
    private $parser;

    /** @var Environment */
    private $environment;

    /** @var Directive[] */
    private $directives;

    /** @var DocumentNode */
    private $document;

    /** @var false|string|null */
    private $specialLetter;

    /** @var ParserDirective|null */
    private $directive;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var LineChecker */
    private $lineChecker;

    /** @var TableParser */
    private $tableParser;

    /** @var Buffer */
    private $buffer;

    /** @var bool */
    private $isCode = false;

    /** @var Lines */
    private $lines;

    /** @var string */
    private $state;

    /** @var TitleNode */
    private $lastTitleNode;

    /** @var TitleNode[] */
    private $openTitleNodes = [];

    /** @var States\CodeState */
    private $stateObject;

    /** @var array<string, States\State> */
    private $states = [];

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param Directive[] $directives
     */
    public function __construct(
        Parser $parser,
        LoggerInterface $logger,
        array $directives
    ) {
        $this->parser = $parser;
        $this->environment = $parser->getEnvironment();
        $this->logger = $logger;
        $this->directives = $directives;

        $this->lineDataParser = new LineDataParser($this->parser);
        $this->lineChecker = new LineChecker($this->lineDataParser);
        $this->tableParser = new TableParser();
        $this->buffer = new Buffer();
    }

    public function parse(string $contents) : DocumentNode
    {
        $this->init();

        $this->document = new DocumentNode($this->environment);
        $this->lines = $this->extractLines($contents);

        $this->initializeStates();

        $this->parseLines();

        return $this->document;
    }

    public function getDocument() : DocumentNode
    {
        return $this->document;
    }

    private function init() : void
    {
        $this->specialLetter = false;
        $this->buffer->clear();
    }

    /**
     * @todo this means that the parser states would be initialized on every run; this is not desirable. Refactor this
     *   to make them into stateless objects
     */
    private function initializeStates() : void
    {
        $environment = $this->parser->getEnvironment();

        $this->states = [
            State::BLOCK => new States\BlockState($this->lineChecker, $this->parser),
            State::CODE => new States\CodeState($this->lineChecker),
            State::COMMENT => new States\CommentState($this->lineChecker),
            State::DEFINITION_LIST => new States\DefinitionListState(
                $this->lineChecker,
                $this->lineDataParser,
                $this->lines
            ),
            State::LIST => new States\ListState($this->lineDataParser, $environment),
            State::PARAGRAPH => new States\ParagraphState($this, $this->lineChecker, $environment),
            State::SEPARATOR => new States\SeparatorState($environment, $this),
            State::TABLE => new States\TableState($this->tableParser, $this->lineChecker, $this->parser),
            State::TITLE => new States\TitleState($environment, $this),
        ];
    }

    /**
     * @todo shouldn't the flush always be executed when transitioning?
     */
    public function transitionTo(string $state, array $extraData = [], bool $flush = false) : void
    {
        if ($flush) {
            $this->flush();
        }

        $this->state = $state;
        $this->stateObject = $this->states[$state] ?? null;
        if ($this->stateObject instanceof States\State) {
            $this->stateObject->enter($this->buffer, $extraData);
        }
    }

    private function extractLines(string $document) : Lines
    {
        $document = trim($document);

        $document = str_replace("\r\n", "\n", $document);
        $document = sprintf("\n%s\n", $document);

        $document = $this->mergeIncludedFiles($document);

        // Removing UTF-8 BOM
        $document = str_replace("\xef\xbb\xbf", '', $document);

        // Replace \u00a0 with " "
        $document = str_replace(chr(194) . chr(160), ' ', $document);

        return new Lines(explode("\n", $document));
    }

    private function parseLines() : void
    {
        $this->transitionTo(State::BEGIN);

        foreach ($this->lines as $line) {
            while (true) {
                if ($this->parseLine($line)) {
                    break;
                }
            }
        }

        // DocumentNode is flushed twice to trigger the directives
        // TODO: Sounds like a workaround; fix this
        $this->flush();
        $this->flush();

        foreach ($this->openTitleNodes as $titleNode) {
            $this->endOpenSection($titleNode);
        }
    }

    /**
     * Parse the given line and return true when parsing is complete, or false to reparse this line.
     *
     * Each line in a document is parsed according to the current state of the statemachine. Sometimes, however, we want
     * to parse a line twice because the first iteration changed the state of the application but the line should be
     * part of another state. This is a look-ahead type of system, where you can inspect the next line and decide to
     * change the state based on it.
     */
    private function parseLine(string $line) : bool
    {
        switch ($this->state) {
            case State::BEGIN:
                return $this->parseBeginState($line);
            case State::PARAGRAPH:
            case State::DEFINITION_LIST:
            case State::LIST:
            case State::COMMENT:
            case State::BLOCK:
            case State::TABLE:
            case State::CODE:
                if (!$this->stateObject->parse($line)) {
                    $this->transitionTo(State::BEGIN, [], true);

                    return false;
                }

                return true;
            case State::DIRECTIVE:
                return $this->parseDirective($line);
            default:
                $this->logger->error('Parser ended in an unexcepted state');
                return true;
        }
    }

    public function flush() : void
    {
        $node = null;

        $this->isCode = false;

        switch ($this->state) {
            case State::BLOCK:
            case State::CODE:
            case State::COMMENT:
            case State::DEFINITION_LIST:
            case State::LIST:
            case State::SEPARATOR:
            case State::TABLE:
                $node = $this->stateObject->leave();

                break;
            case State::PARAGRAPH:
                $this->isCode = $this->prepareCode();
                $node = $this->stateObject->leave();

                break;
            case State::TITLE:
                $node = $this->stateObject->leave();

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

                $this->lastTitleNode = $node;
                $this->document->addNode(new SectionBeginNode($node));
                $this->openTitleNodes[] = $node;

                break;
        }

        // TODO: Why is this not in the switch above with State::DIRECTIVE? Is this a sub-parsing mechanism?
        if ($this->directive !== null) {
            $this->flushDirective($node);

            $node = null;
            $this->directive = null;
        }

        if ($node !== null) {
            $this->document->addNode($node);
        }

        $this->init();
    }

    private function getCurrentDirective() : ?Directive
    {
        if ($this->directive === null) {
            return null;
        }

        $name = $this->directive->getName();

        return $this->directives[$name];
    }

    private function isDirectiveOption(string $line) : bool
    {
        if ($this->directive === null) {
            return false;
        }

        $directiveOption = $this->lineDataParser->parseDirectiveOption($line);

        if ($directiveOption === null) {
            return false;
        }

        $this->directive->setOption($directiveOption->getName(), $directiveOption->getValue());

        return true;
    }

    private function initDirective(string $line) : bool
    {
        $this->flush();
        $parserDirective = $this->lineDataParser->parseDirective($line);

        if ($parserDirective === null) {
            return false;
        }

        if (!isset($this->directives[$parserDirective->getName()])) {
            $this->logger->error(
                sprintf(
                    'Unknown directive: "%s" %sfor line "%s"',
                    $parserDirective->getName(),
                    $this->environment->getCurrentFileName() !== '' ? sprintf(
                        'in "%s" ',
                        $this->environment->getCurrentFileName()
                    ) : '',
                    $line
                )
            );

            return false;
        }

        $this->directive = $parserDirective;

        return true;
    }

    public function getSpecialLetter() : string
    {
        return $this->specialLetter;
    }

    public function setSpecialLetter(string $specialLetter) : void
    {
        $this->specialLetter = $specialLetter;
    }

    private function prepareCode() : bool
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

    private function parseLink(string $line) : bool
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

    private function endOpenSection(TitleNode $titleNode) : void
    {
        $this->document->addNode(new SectionEndNode($titleNode));

        $key = array_search($titleNode, $this->openTitleNodes, true);

        if ($key === false) {
            return;
        }

        unset($this->openTitleNodes[$key]);
    }

    public function mergeIncludedFiles(string $document) : string
    {
        return preg_replace_callback(
            '/^\.\. include:: (.+)$/m',
            function ($match) {
                $path = $this->environment->absoluteRelativePath($match[1]);

                $origin = $this->environment->getOrigin();
                if (!$origin->has($path)) {
                    throw new RuntimeException(
                        sprintf('Include "%s" (%s) does not exist or is not readable.', $match[0], $path)
                    );
                }

                $contents = $origin->read($path);

                if ($contents === false) {
                    throw new RuntimeException(sprintf('Could not load file from path %s', $path));
                }

                return $this->mergeIncludedFiles($contents);
            },
            $document
        );
    }

    private function parseBeginState(string $line) : bool
    {
        if (trim($line) === '') {
            return true;
        }

        if ($this->lineChecker->isListLine($line, $this->isCode)) {
            $this->transitionTo(State::LIST);

            return false;
        }

        if ($this->lineChecker->isBlockLine($line)) {
            $this->transitionTo($this->isCode ? State::CODE : State::BLOCK);

            return false;
        }

        if ($this->parseLink($line)) {
            return true;
        }

        if ($this->lineChecker->isDirective($line)) {
            $this->transitionTo(State::DIRECTIVE);
            $this->buffer->clear();
            $this->initDirective($line);

            return true;
        }

        if ($this->lineChecker->isDefinitionList($this->lines->getNextLine())) {
            $this->transitionTo(State::DEFINITION_LIST);
            $this->buffer->push($line);

            return true;
        }

        $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

        if ($separatorLineConfig === null) {
            $this->transitionTo(State::PARAGRAPH);

            return false;
        }

        $this->transitionTo(
            State::TABLE,
            [
                'line' => $line,
                'separatorLineConfig' => $separatorLineConfig
            ]
        );

        return true;
    }

    private function parseDirective(string $line) : bool
    {
        if ($this->isDirectiveOption($line)) {
            return true;
        }

        if (!$this->lineChecker->isDirective($line)) {
            $directive = $this->getCurrentDirective();
            $this->isCode = $directive !== null ? $directive->wantCode() : false;
            $this->transitionTo(State::BEGIN);

            return false;
        }

        $this->initDirective($line);

        return true;
    }

    /**
     * @param Node|null $node
     */
    private function flushDirective(?Node $node) : void
    {
        $currentDirective = $this->getCurrentDirective();

        if ($currentDirective === null) {
            return;
        }

        try {
            $currentDirective->process(
                $this->parser,
                $node,
                $this->directive->getVariable(),
                $this->directive->getData(),
                $this->directive->getOptions()
            );
        } catch (Throwable $e) {
            $this->logger->error(
                sprintf(
                    'Error while processing "%s" directive%s: %s',
                    $currentDirective->getName(),
                    $this->environment->getCurrentFileName() !== '' ? sprintf(
                        ' in "%s"',
                        $this->environment->getCurrentFileName()
                    ) : '',
                    $e->getMessage()
                )
            );
        }
    }
}
