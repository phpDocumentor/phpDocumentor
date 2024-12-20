<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline;

class PipelineBuilder
{
    /** @var callable[] */
    private array $stages = [];

    public function add(callable $stage): self
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function build(): PipelineInterface
    {
        return new Pipeline(null, ...$this->stages);
    }
}
