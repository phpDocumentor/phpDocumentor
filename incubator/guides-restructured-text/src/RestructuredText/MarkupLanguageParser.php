<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\MarkupLanguageParser as ParserInterface;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use RuntimeException;

use function strtolower;

class MarkupLanguageParser implements ParserInterface
{
    /** @var ParserContext|null */
    private $environment;

    /** @var Directive[] */
    private $directives;

    /** @var string|null */
    private $filename = null;

    /** @var DocumentParser|null */
    private $documentParser;

    /**
     * @param iterable<Directive> $directives
     */
    public function __construct(
        iterable $directives
    ) {
        foreach ($directives as $directive) {
            $this->registerDirective($directive);
        }
    }

    public function supports(string $inputFormat): bool
    {
        return strtolower($inputFormat) === 'rst';
    }

    public function getSubParser(): MarkupLanguageParser
    {
        return new MarkupLanguageParser(
            $this->directives
        );
    }

    public function getEnvironment(): ParserContext
    {
        if ($this->environment === null) {
            throw new RuntimeException(
                'A parser\'s Environment should not be consulted before parsing has started'
            );
        }

        return $this->environment;
    }

    private function registerDirective(Directive $directive): void
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

    public function parse(ParserContext $environment, string $contents): DocumentNode
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
        return new DocumentParser($this, $this->directives);
    }
}
