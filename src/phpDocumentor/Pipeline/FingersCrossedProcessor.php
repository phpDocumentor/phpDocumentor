<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline;

class FingersCrossedProcessor implements Processor
{
    public function process(mixed $payload, callable ...$stages): mixed
    {
        foreach ($stages as $stage) {
            $payload = $stage($payload);
        }

        return $payload;
    }
}
