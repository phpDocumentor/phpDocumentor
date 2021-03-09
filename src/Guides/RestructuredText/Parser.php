<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\References\Doc;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use Psr\Log\LoggerInterface;
use RuntimeException;
use function array_merge;

class Parser implements ParserInterface
{
    /** @var Environment */
    private $environment;

    /** @var Directive[] */
    private $directives;

    /** @var string|null */
    private $filename = null;

    /** @var DocumentParser|null */
    private $documentParser;

    /** @var array<Reference> */
    private $references;

    /** @var Format */
    private $format;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param array<Directive> $directives
     * @param array<Reference> $references
     */
    public function __construct(
        Format $format,
        Environment $environment,
        LoggerInterface $logger,
        array $directives,
        array $references
    ) {
        $this->format = $format;
        $this->environment = $environment;
        $this->directives = $directives;
        $this->references = $references;

        $this->initDirectives($directives);
        $this->initReferences($references);
        $this->logger = $logger;
    }

    public function getSubParser() : Parser
    {
        return new Parser(
            $this->format,
            $this->environment,
            $this->logger,
            $this->directives,
            $this->references
        );
    }

    /**
     * @param array<Directive> $directives
     */
    public function initDirectives(array $directives) : void
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

    private function createDocumentParser() : DocumentParser
    {
        return new DocumentParser(
            $this,
            $this->logger,
            $this->directives
        );
    }
}
