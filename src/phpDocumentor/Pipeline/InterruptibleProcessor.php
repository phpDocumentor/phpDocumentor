<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline;

final class InterruptibleProcessor implements Processor
{
    /** @var callable */
    private $continue;

    public function __construct(callable $continue)
    {
        $this->continue = $continue;
    }

    public function process(mixed $payload, callable ...$stages): mixed
    {
        foreach ($stages as $stage) {
            $payload = $stage($payload);
            if (! ($this->continue)($payload)) {
                break;
            }
        }

        return $payload;
    }
}
