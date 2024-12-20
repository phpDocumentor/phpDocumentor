<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline;

final class Pipeline implements PipelineInterface
{
    /** @var callable[] */
    private array $stages;

    public function __construct(
        private Processor|null $procesor = null,
        callable ...$stages,
    ) {
        $this->procesor = $procesor ?? new FingersCrossedProcessor();
        $this->stages = $stages;
    }

    public function pipe(callable $stage): PipelineInterface
    {
        $pipeline = clone $this;
        $pipeline->stages[] = $stage;

        return $pipeline;
    }

    public function process(mixed $payload): mixed
    {
        return $this->procesor->process($payload, ...$this->stages);
    }

    public function __invoke(mixed $payload): mixed
    {
        return $this->process($payload);
    }
}
