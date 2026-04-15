<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

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
