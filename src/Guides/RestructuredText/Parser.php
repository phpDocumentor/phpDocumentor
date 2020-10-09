<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Doctrine\Common\EventManager;
use InvalidArgumentException;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Formats\Format;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Nodes\NodeTypes;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\NodeFactory\DefaultNodeFactory;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeFactory;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeInstantiator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\TemplateRenderer;
use RuntimeException;
use function sprintf;

class Parser implements ParserInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var Environment */
    private $environment;

    /** @var Directive[] */
    private $directives = [];

    /** @var bool */
    private $includeAllowed = true;

    /** @var string */
    private $includeRoot = '';

    /** @var string|null */
    private $filename = null;

    /** @var DocumentParser|null */
    private $documentParser;

    /** @var NodeFactory */
    private $nodeFactory;

    /** @var array<Nodes\Node> */
    private $nodeRegistry = [];

    /** @var array<Reference> */
    private $references = [];

    /** @var EventManager */
    private $eventManager;

    /** @var Format */
    private $format;

    /** @var NodeRendererFactory[]  */
    private $nodeRendererFactories;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(Configuration $configuration, Environment $environment, array $directives, array $references)
    {
        $this->configuration = $configuration;
        $this->environment = $environment;
        $this->directives = $directives;
        $this->references = $references;
        $this->eventManager = $configuration->getEventManager();
        $this->format = $configuration->getFormat();
        $this->nodeRendererFactories = $this->format->getNodeRendererFactories();
        $this->templateRenderer = $this->configuration->getTemplateRenderer();

        $this->nodeRegistry = [
            NodeTypes::DOCUMENT => Nodes\DocumentNode::class,
            NodeTypes::SPAN => Nodes\SpanNode::class,
            NodeTypes::TOC => Nodes\TocNode::class,
            NodeTypes::TITLE => Nodes\TitleNode::class,
            NodeTypes::SEPARATOR => Nodes\SeparatorNode::class,
            NodeTypes::CODE => Nodes\CodeNode::class,
            NodeTypes::QUOTE => Nodes\QuoteNode::class,
            NodeTypes::PARAGRAPH => Nodes\ParagraphNode::class,
            NodeTypes::ANCHOR => Nodes\AnchorNode::class,
            NodeTypes::LIST => Nodes\ListNode::class,
            NodeTypes::TABLE => Nodes\TableNode::class,
            NodeTypes::DEFINITION_LIST => Nodes\DefinitionListNode::class,
            NodeTypes::WRAPPER => Nodes\WrapperNode::class,
            NodeTypes::FIGURE => Nodes\FigureNode::class,
            NodeTypes::IMAGE => Nodes\ImageNode::class,
            NodeTypes::META => Nodes\MetaNode::class,
            NodeTypes::RAW => Nodes\RawNode::class,
            NodeTypes::DUMMY => Nodes\DummyNode::class,
            NodeTypes::MAIN => Nodes\MainNode::class,
            NodeTypes::BLOCK => Nodes\BlockNode::class,
            NodeTypes::CALLABLE => Nodes\CallableNode::class,
            NodeTypes::SECTION_BEGIN => Nodes\SectionBeginNode::class,
            NodeTypes::SECTION_END => Nodes\SectionEndNode::class
        ];

        $this->initDirectives($directives);
        $this->initReferences($references);
        $this->environment->setNodeFactory($this->getNodeFactory());
    }

    public function getSubParser() : Parser
    {
        return new Parser($this->configuration, $this->environment, $this->directives, $this->references);
    }

    public function getNodeFactory() : NodeFactory
    {
        if ($this->nodeFactory !== null) {
            return $this->nodeFactory;
        }

        $instantiators = [];
        foreach ($this->nodeRegistry as $nodeName => $nodeClass) {
            $instantiators[] = $this->createNodeInstantiator($nodeName, $nodeClass);
        }
        return new DefaultNodeFactory($this->eventManager, ...$instantiators);
    }

    public function renderTemplate(string $template, array $parameters = []) : string
    {
        return $this->templateRenderer->render($template, $parameters);
    }

    public function initDirectives(array $directives) : void
    {
        $directives = array_merge(
            [
                new Directives\Dummy(),
                new Directives\CodeBlock(),
                new Directives\Raw(),
                new Directives\Replace(),
                new Directives\Toctree(),
            ],
            $this->format->getDirectives(),
            $directives
        );

        foreach ($directives as $directive) {
            $this->registerDirective($directive);
        }
    }

    public function initReferences(array $references) : void
    {
        $references = array_merge(
            [
                new Doc(),
                new Doc('ref', true),
            ],
            $references
        );

        foreach ($references as $reference) {
            $this->environment->registerReference($reference);
        }
    }

    public function getEnvironment() : Environment
    {
        return $this->environment;
    }

    public function registerDirective(Directive $directive) : void
    {
        $this->directives[$directive->getName()] = $directive;
    }

    public function getDocument() : DocumentNode
    {
        if ($this->documentParser === null) {
            throw new RuntimeException('Nothing has been parsed yet.');
        }

        return $this->documentParser->getDocument();
    }

    public function getFilename() : string
    {
        return $this->filename ?: '(unknown)';
    }

    public function getIncludeAllowed() : bool
    {
        return $this->includeAllowed;
    }

    public function getIncludeRoot() : string
    {
        return $this->includeRoot;
    }

    public function setIncludePolicy(bool $includeAllowed, ?string $directory = null) : self
    {
        $this->includeAllowed = $includeAllowed;

        if ($directory !== null) {
            $this->includeRoot = $directory;
        }

        return $this;
    }

    /**
     * @param string|string[]|SpanNode $span
     */
    public function createSpanNode($span) : SpanNode
    {
        return $this->getNodeFactory()->createSpanNode($this, $span);
    }

    public function parse(string $contents) : DocumentNode
    {
        $this->getEnvironment()->reset();

        return $this->parseLocal($contents);
    }

    public function parseLocal(string $contents) : DocumentNode
    {
        $this->documentParser = $this->createDocumentParser();

        return $this->documentParser->parse($contents);
    }

    public function parseFragment(string $contents) : DocumentNode
    {
        return $this->createDocumentParser()->parse($contents);
    }

    public function parseFile(string $file) : DocumentNode
    {
        $origin = $this->environment->getOrigin();
        if (! $origin->has($file)) {
            throw new InvalidArgumentException(sprintf('File at path %s does not exist', $file));
        }

        $this->filename = $file;

        $contents = $origin->read($file);

        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Could not load file from path %s', $file));
        }

        return $this->parse($contents);
    }

    private function createDocumentParser() : DocumentParser
    {
        return new DocumentParser(
            $this,
            $this->environment,
            $this->getNodeFactory(),
            $this->eventManager,
            $this->directives,
            $this->includeAllowed,
            $this->includeRoot
        );
    }

    private function createNodeInstantiator(string $type, string $nodeClassName) : NodeInstantiator
    {
        return new NodeInstantiator(
            $type,
            $nodeClassName,
            $this->nodeRendererFactories[$nodeClassName] ?? null,
            $this->eventManager
        );
    }
}
