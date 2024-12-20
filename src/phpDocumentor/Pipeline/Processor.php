<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline;

interface Processor
{
    public function process(mixed $payload, callable ...$stages): mixed;
}
