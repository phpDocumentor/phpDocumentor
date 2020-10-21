<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Doctrine\Common\EventManager;
use InvalidArgumentException;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeFactory;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use RuntimeException;
use Webmozart\Assert\Assert;
use function array_merge;
use function sprintf;

class Parser implements ParserInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var Environment */
    private $environment;

    /** @var Directive[] */
    private $directives;

    /** @var string|null */
    private $filename = null;

    /** @var DocumentParser|null */
    private $documentParser;

    /** @var NodeFactory */
    private $nodeFactory;

    /** @var array<Reference> */
    private $references;

    /** @var EventManager */
    private $eventManager;

    /** @var Format */
    private $format;

    /**
     * @param array<Directive> $directives
     * @param array<Reference> $references
     */
    public function __construct(
        Configuration $configuration,
        Environment $environment,
        EventManager $eventManager,
        NodeFactory $nodeFactory,
        array $directives,
        array $references
    ) {
        $this->configuration = $configuration;
        $this->environment = $environment;
        $this->directives = $directives;
        $this->references = $references;
        $this->eventManager = $eventManager;
        $this->nodeFactory = $nodeFactory;
        Assert::isInstanceOf($configuration->getFormat(), Format::class);
        $this->format = $configuration->getFormat();

        $this->initDirectives($directives);
        $this->initReferences($references);
        $this->environment->setNodeFactory($nodeFactory);
    }

    public function getSubParser() : Parser
    {
        return new Parser(
            $this->configuration,
            $this->environment,
            $this->eventManager,
            $this->nodeFactory,
            $this->directives,
            $this->references
        );
    }

    public function getNodeFactory() : NodeFactory
    {
        return $this->nodeFactory;
    }

    /**
     * @param array<Directive> $directives
     */
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

    /**
     * @param array<Reference> $references
     */
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
        if (!$origin->has($file)) {
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
            $this->directives
        );
    }
}
