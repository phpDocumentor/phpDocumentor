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

use phpDocumentor\Pipeline\Stage\TimedStageDecorator;
use Psr\Log\LoggerInterface;

final class PipelineFactory
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /** @param iterable<callable> $stages */
    public function create(iterable $stages): PipelineInterface
    {
        $builder = new PipelineBuilder();
        foreach ($stages as $stage) {
            $builder->add(new TimedStageDecorator($this->logger, $stage));
        }

        return $builder->build();
    }
}
