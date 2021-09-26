<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

final class DirectiveRule implements Rule
{
    /** @var Parser */
    private $parser;

    /** @var DocumentParser */
    private $documentParser;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var LiteralBlockRule */
    private $literalBlockRule;

    /** @var Directive[] */
    private $directives;

    public function __construct(
        Parser $parser,
        DocumentParser $documentParser,
        LineDataParser $lineDataParser,
        LiteralBlockRule $literalBlockRule,
        array $directives = []
    ) {
        $this->parser = $parser;
        $this->lineDataParser = $lineDataParser;
        $this->directives = $directives;
        $this->literalBlockRule = $literalBlockRule;
        $this->documentParser = $documentParser;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $this->isDirective($documentParser->getDocumentIterator()->current());
    }

    public function apply(DocumentIterator $documentIterator): ?Node
    {
        $openingLine = $documentIterator->current();
        $documentIterator->next();
        $directive = $this->lineDataParser->parseDirective($openingLine);

        if ($directive === null) {
            return null;
        }

        $directiveHandler = $this->getDirectiveHandler($directive);
        if ($directiveHandler === null) {
            $message = sprintf(
                'Unknown directive: "%s" %sfor line "%s"',
                $directive->getName(),
                $this->parser->getEnvironment()->getCurrentFileName() !== '' ? sprintf(
                    'in "%s" ',
                    $this->parser->getEnvironment()->getCurrentFileName()
                ) : '',
                $openingLine
            );

            $this->parser->getEnvironment()->addError($message);

            return null;
        }

        $this->interpretDirectiveOptions($documentIterator, $directive);

        // Processing the Directive, the handler is responsible for adding the right Nodes to the document.
        try {
            $directiveHandler->process(
                $this->parser,
                $this->interpretContentBlock($documentIterator),
                $directive->getVariable(),
                $directive->getData(),
                $directive->getOptions()
            );
        } catch (\Throwable $e) {
            $message = sprintf(
                'Error while processing "%s" directive%s: %s',
                $directiveHandler->getName(),
                $this->parser->getEnvironment()->getCurrentFileName() !== '' ? sprintf(
                    ' in "%s"',
                    $this->parser->getEnvironment()->getCurrentFileName()
                ) : '',
                $e->getMessage()
            );

            $this->parser->getEnvironment()->addError($message);
        }

        return null;
    }

    public function isDirective(string $line): bool
    {
        return preg_match('/^\.\. (\|(.+)\| |)([^\s]+)::( (.*)|)$/mUsi', $line) > 0;
    }

    public function getDirectiveHandler(Directive $directive): ?DirectiveHandler
    {
        return $this->directives[$directive->getName()] ?? null;
    }

    private function interpretDirectiveOptions(DocumentIterator $documentIterator, Directive $directive): void
    {
        while (
            $documentIterator->valid()
            && ($directiveOption = $this->lineDataParser->parseDirectiveOption($documentIterator->current())) !== null
        ) {
            $directive->setOption($directiveOption->getName(), $directiveOption->getValue());

            $documentIterator->next();
        }
    }

    private function interpretContentBlock(DocumentIterator $documentIterator): ?Node
    {
        $contentBlock = null;
        $this->documentParser->nextIndentedBlockShouldBeALiteralBlock = true;
        if ($documentIterator->valid() && $this->literalBlockRule->applies($this->documentParser)) {
            $contentBlock = $this->literalBlockRule->apply($documentIterator);
        }

        return $contentBlock;
    }
}
