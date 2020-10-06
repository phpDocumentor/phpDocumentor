<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\NodeFactory\DefaultNodeFactory;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeFactory;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeInstantiator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use RuntimeException;
use function sprintf;

class Parser implements ParserInterface
{
    /** @var Kernel */
    private $kernel;

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

    /** @var array */
    private $nodeRegistry = [];

    public function __construct(Kernel $kernel, Environment $environment)
    {
        $this->kernel        = $kernel;
        $this->environment   = $environment;

        $this->environment->setNodeFactory($this->getNodeFactory());
        $this->initDirectives();
        $this->initReferences();
        $this->nodeRegistry = $kernel->getNodes();
    }

    public function getSubParser() : Parser
    {
        return new Parser($this->kernel, $this->environment);
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
        return new DefaultNodeFactory($this->kernel->getConfiguration()->getEventManager(), ...$instantiators);
    }

    public function renderTemplate(string $template, array $parameters = []) : string
    {
        return $this->kernel->getConfiguration()->getTemplateRenderer()->render($template, $parameters);
    }

    public function initDirectives() : void
    {
        $directives = $this->kernel->getDirectives();

        foreach ($directives as $directive) {
            $this->registerDirective($directive);
        }
    }

    public function initReferences() : void
    {
        $references = $this->kernel->getReferences();

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
            $this->kernel->getConfiguration()->getEventManager(),
            $this->directives,
            $this->includeAllowed,
            $this->includeRoot
        );
    }

    private function createNodeInstantiator(string $type, string $nodeClassName) : NodeInstantiator
    {
        $configuration = $this->kernel->getConfiguration();
        $nodeRendererFactory = $configuration->getFormat()->getNodeRendererFactories()[$nodeClassName] ?? null;

        return new NodeInstantiator(
            $type,
            $nodeClassName,
            $nodeRendererFactory,
            $configuration->getEventManager()
        );
    }
}
