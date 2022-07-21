<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Query;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\JsonPath\Executor;
use phpDocumentor\JsonPath\Parser;

class Engine
{
    private Executor $executor;
    private Parser $parser;

    public function __construct(Executor $executor, Parser $parser)
    {
        $this->executor = $executor;
        $this->parser = $parser;
    }

    /** @return mixed */
    public function perform(Descriptor $descriptor, string $query)
    {
        return $this->executor->evaluate($this->parser->parse($query), $descriptor);
    }
}
