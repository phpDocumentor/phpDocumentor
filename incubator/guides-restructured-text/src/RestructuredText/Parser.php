<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Formats\OutputFormat;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\OutputFormat as RestructuredTextOutputFormat;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use RuntimeException;

use function array_merge;
use function is_array;
use function iterator_to_array;
use function strtolower;

class Parser implements ParserInterface
{
    /** @var Environment|null */
    private $environment;

    /** @var Directive[] */
    private $directives;

    /** @var string|null */
    private $filename = null;

    /** @var DocumentParser|null */
    private $documentParser;

    /** @var array<Reference> */
    private $references;

    /** @var EventManager */
    private $eventManager;

    /** @var OutputFormat */
    private $format;

    /** @var ReferenceBuilder */
    private $referenceBuilder;

    /**
     * @param iterable<Directive> $directives
     * @param iterable<Reference> $references
     */
    public function __construct(
        RestructuredTextOutputFormat $format,
        ReferenceBuilder $referenceRegistry,
        EventManager $eventManager,
        iterable $directives,
        iterable $references
    ) {
        $this->format = $format;
        $this->referenceBuilder = $referenceRegistry;
        $this->eventManager = $eventManager;
        $this->directives = is_array($directives) ? $directives : iterator_to_array($directives);
        $this->references = is_array($references) ? $references : iterator_to_array($references);

        $this->initDirectives($this->directives);
        $this->initReferences($this->references);
    }

    public function supports(string $inputFormat, OutputFormat $outputFormat): bool
    {
        return strtolower($inputFormat) === 'rst' && $this->format === $outputFormat;
    }

    public function getSubParser(): Parser
    {
        return new Parser(
            $this->format,
            $this->referenceBuilder,
            $this->eventManager,
            $this->directives,
            $this->references
        );
    }

    /**
     * @param array<Directive> $directives
     */
    public function initDirectives(array $directives): void
    {
        $directives = array_merge(
            $directives,
            $this->format->getDirectives()
        );

        foreach ($directives as $directive) {
            $this->registerDirective($directive);
        }
    }

    /**
     * @param array<Reference> $references
     */
    public function initReferences(array $references): void
    {
        $references = array_merge(
            [
                new Doc(),
                new Doc('ref', true),
            ],
            $references
        );

        foreach ($references as $reference) {
            $this->referenceBuilder->registerTypeOfReference($reference);
        }
    }

    public function getEnvironment(): Environment
    {
        if ($this->environment === null) {
            throw new RuntimeException(
                'A parser\'s Environment should not be consulted before parsing has started'
            );
        }

        return $this->environment;
    }

    public function registerDirective(Directive $directive): void
    {
        $this->directives[$directive->getName()] = $directive;
        foreach ($directive->getAliases() as $alias) {
            $this->directives[$alias] = $directive;
        }
    }

    public function getDocument(): DocumentNode
    {
        if ($this->documentParser === null) {
            throw new RuntimeException('Nothing has been parsed yet.');
        }

        return $this->documentParser->getDocument();
    }

    public function getFilename(): string
    {
        return $this->filename ?: '(unknown)';
    }

    public function parse(Environment $environment, string $contents): DocumentNode
    {
        $this->environment = $environment;
        $this->documentParser = $this->createDocumentParser();

        return $this->documentParser->parse($contents);
    }

    public function parseFragment(string $contents): DocumentNode
    {
        return $this->createDocumentParser()->parse($contents);
    }

    private function createDocumentParser(): DocumentParser
    {
        return new DocumentParser($this, $this->eventManager, $this->directives);
    }

    public function getReferenceBuilder(): ReferenceBuilder
    {
        return $this->referenceBuilder;
    }

    public function getNodeRendererFactory(): NodeRendererFactory
    {
        return $this->format->getNodeRendererFactory();
    }
}
