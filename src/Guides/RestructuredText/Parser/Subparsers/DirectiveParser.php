<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use ArrayObject;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

final class DirectiveParser implements Subparser
{
    /** @var LineChecker */
    private $lineChecker;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var Environment */
    private $environment;

    /** @var ArrayObject */
    private $directives;

    /** @var ?Directive */
    private $directive;

    public function __construct(Environment $environment, LineChecker $lineChecker, LineDataParser $lineDataParser, ArrayObject $directives)
    {
        $this->lineDataParser = $lineDataParser;
        $this->lineChecker = $lineChecker;
        $this->environment = $environment;
        $this->directives = $directives;
    }

    public function init(string $line): ?Directive
    {
        $directive = $this->lineDataParser->parseDirective($line);

        if ($directive === null) {
            return null;
        }

        if (!isset($this->directives[$directive->getName()])) {
            $message = sprintf(
                'Unknown directive: "%s" %sfor line "%s"',
                $directive->getName(),
                $this->environment->getCurrentFileName() !== '' ? sprintf(
                    'in "%s" ',
                    $this->environment->getCurrentFileName()
                ) : '',
                $line
            );

            $this->environment->addError($message);

            return null;
        }

        $this->directive = $directive;

        return $directive;
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
        if (!$this->lineChecker->isDirective($line)) {
            $directive = $this->getDirectiveHandler();
            $this->isCode = $directive !== null ? $directive->wantCode() : false;

            return false;
        }

        return false;
    }

    public function build(): ?Node
    {
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
