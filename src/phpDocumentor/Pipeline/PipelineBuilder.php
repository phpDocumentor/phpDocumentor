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
