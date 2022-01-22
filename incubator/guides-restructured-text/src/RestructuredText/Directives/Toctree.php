<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Toc\ToctreeBuilder;

class Toctree extends Directive
{
    /** @var ToctreeBuilder */
    private $toctreeBuilder;

    public function __construct(ToctreeBuilder $toctreeBuilder)
    {
        $this->toctreeBuilder = $toctreeBuilder;
    }

    public function getName(): string
    {
        return 'toctree';
    }

    /**
     * @param string[] $options
     */
    public function process(
        MarkupLanguageParser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ): void {
        if ($node === null) {
            return;
        }

        $environment = $parser->getEnvironment();

        $toctreeFiles = $this->toctreeBuilder->buildToctreeFiles($environment, $node, $options);
        $parser->getDocument()->addNode((new TocNode($toctreeFiles))->withOptions($options));
    }
}
