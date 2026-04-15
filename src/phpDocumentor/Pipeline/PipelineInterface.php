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

interface PipelineInterface
{
    public function pipe(callable $stage): PipelineInterface;

    public function process(mixed $payload): mixed;

    public function __invoke(mixed $payload): mixed;
}
