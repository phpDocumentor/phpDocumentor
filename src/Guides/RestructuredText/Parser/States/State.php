<?php

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;

interface State
{
    public function getName() : string;

    public function enter(Buffer $buffer, array $extraState = []) : void;

    public function parse(string $line) : bool;

    public function leave() : ?Node;
}
