<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use ArrayObject;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
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

    public function __construct(Environment $environment, LineChecker $lineChecker, LineDataParser $lineDataParser, ArrayObject $directives)
    {
        $this->lineDataParser = $lineDataParser;
        $this->lineChecker = $lineChecker;
        $this->environment = $environment;
        $this->directives = $directives;
    }

    public function init(string $line): ?Directive
    {
        $parserDirective = $this->lineDataParser->parseDirective($line);

        if ($parserDirective === null) {
            return null;
        }

        if (!isset($this->directives[$parserDirective->getName()])) {
            $message = sprintf(
                'Unknown directive: "%s" %sfor line "%s"',
                $parserDirective->getName(),
                $this->environment->getCurrentFileName() !== '' ? sprintf(
                    'in "%s" ',
                    $this->environment->getCurrentFileName()
                ) : '',
                $line
            );

            $this->environment->addError($message);

            return null;
        }

        return $parserDirective;
    }

    public function parse(string $line): bool
    {
        return false;
    }

    public function build(): ?Node
    {
        return null;
    }
}
