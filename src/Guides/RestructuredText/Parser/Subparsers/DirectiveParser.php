<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use ArrayObject;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

final class DirectiveParser implements Subparser
{
    /** @var LineChecker */
    private $lineChecker;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var Parser */
    private $parser;

    /** @var Environment */
    private $environment;

    /** @var ArrayObject */
    private $directives;

    /** @var ?Directive */
    private $directive;

    /** @var Node|null */
    private $contentBlock;

    public function __construct(Parser $parser, LineChecker $lineChecker, LineDataParser $lineDataParser, ArrayObject $directives)
    {
        $this->lineDataParser = $lineDataParser;
        $this->lineChecker = $lineChecker;
        $this->parser = $parser;
        $this->environment = $parser->getEnvironment();
        $this->directives = $directives;
    }

    public function reset(string $openingLine): void
    {
        $directive = $this->lineDataParser->parseDirective($openingLine);

        if ($directive === null) {
            return;
        }

        if (!isset($this->directives[$directive->getName()])) {
            $message = sprintf(
                'Unknown directive: "%s" %sfor line "%s"',
                $directive->getName(),
                $this->environment->getCurrentFileName() !== '' ? sprintf(
                    'in "%s" ',
                    $this->environment->getCurrentFileName()
                ) : '',
                $openingLine
            );

            $this->environment->addError($message);

            return;
        }

        $this->directive = $directive;
        $this->contentBlock = null;
    }

    /**
     * @return Directive|null
     */
    public function getDirective(): ?Directive
    {
        return $this->directive;
    }

    public function parse(string $line): bool
    {
        if ($this->lineChecker->isDirective($line) === false) {
            return false;
        }

        return true;
    }

    public function setContentBlock(?Node $node): void
    {
        $this->contentBlock = $node instanceof CodeNode ? $node : null;
    }

    public function build(): ?Node
    {
        $directiveHandler = $this->getDirectiveHandler();

        if ($directiveHandler !== null) {
            try {
                $directiveHandler->process(
                    $this->parser,
                    $this->contentBlock,
                    $this->directive->getVariable(),
                    $this->directive->getData(),
                    $this->directive->getOptions()
                );
            } catch (\Throwable $e) {
                $message = sprintf(
                    'Error while processing "%s" directive%s: %s',
                    $directiveHandler->getName(),
                    $this->environment->getCurrentFileName() !== '' ? sprintf(
                        ' in "%s"',
                        $this->environment->getCurrentFileName()
                    ) : '',
                    $e->getMessage()
                );

                $this->environment->addError($message);
            }
        }

        return null;
    }

    public function getDirectiveHandler(): ?DirectiveHandler
    {
        if ($this->directive === null) {
            return null;
        }

        $name = $this->directive->getName();

        return $this->directives[$name];
    }
}
