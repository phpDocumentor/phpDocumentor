<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;
use Throwable;

use function preg_match;
use function sprintf;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#directives
 */
final class DirectiveRule implements Rule
{
    /** @var MarkupLanguageParser */
    private $parser;

    /** @var DocumentParser */
    private $documentParser;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var LiteralBlockRule */
    private $literalBlockRule;

    /** @var DirectiveHandler[] */
    private $directives;

    /**
     * @param DirectiveHandler[] $directives
     */
    public function __construct(
        MarkupLanguageParser $parser,
        DocumentParser $documentParser,
        LineDataParser $lineDataParser,
        LiteralBlockRule $literalBlockRule,
        array $directives = []
    ) {
        $this->parser = $parser;
        $this->lineDataParser = $lineDataParser;
        $this->literalBlockRule = $literalBlockRule;
        $this->documentParser = $documentParser;
        $this->directives = $directives;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        return $this->isDirective($documentParser->getDocumentIterator()->current());
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
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
        } catch (Throwable $e) {
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

    private function interpretDirectiveOptions(LinesIterator $documentIterator, Directive $directive): void
    {
        while (
            $documentIterator->valid()
            && ($directiveOption = $this->lineDataParser->parseDirectiveOption($documentIterator->current())) !== null
        ) {
            $directive->setOption($directiveOption->getName(), $directiveOption->getValue());

            $documentIterator->next();
        }
    }

    private function interpretContentBlock(LinesIterator $documentIterator): ?Node
    {
        $contentBlock = null;
        $this->documentParser->nextIndentedBlockShouldBeALiteralBlock = true;
        if ($documentIterator->valid() && $this->literalBlockRule->applies($this->documentParser)) {
            $contentBlock = $this->literalBlockRule->apply($documentIterator);
        }

        return $contentBlock;
    }
}
