<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline;

interface PipelineInterface
{
    public function pipe(callable $stage): PipelineInterface;

    public function process(mixed $payload): mixed;

    public function __invoke(mixed $payload): mixed;
}
