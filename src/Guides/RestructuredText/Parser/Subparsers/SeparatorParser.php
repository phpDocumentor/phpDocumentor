<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\RestructuredText\Parser;

final class SeparatorParser implements Subparser
{
    /** @var Environment */
    private $environment;

    /** @var string */
    private $specialLetter;

    public function __construct(Parser $parser, string $specialLetter)
    {
        $this->environment = $parser->getEnvironment();
        $this->specialLetter = $specialLetter;
    }

    public function reset(string $openingLine): void
    {
    }

    public function parse(string $line): bool
    {
        return false;
    }

    public function build(): ?Node
    {
        return new SeparatorNode($this->environment->getLevel((string) $this->specialLetter));
    }
}
