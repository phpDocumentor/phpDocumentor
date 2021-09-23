<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use phpDocumentor\Guides\Nodes\Node;

interface Subparser
{
    public function parse(string $line): bool;

    public function build(): ?Node;
}
