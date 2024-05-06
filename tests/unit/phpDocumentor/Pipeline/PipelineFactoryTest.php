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

use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

/** @coversDefaultClass \phpDocumentor\Pipeline\PipelineFactory */
final class PipelineFactoryTest extends TestCase
{
    public function test_creates_a_pipeline_with_the_given_series_of_stages(): void
    {
        $pipelineFactory = new PipelineFactory(new TestLogger());
        $pipeline = $pipelineFactory->create(
            [
                static fn ($value) => $value + 1,
                static fn ($value) => $value * 2,
            ],
        );

        // can only test whether it worked by running the pipeline and
        // getting the expected output
        $this->assertSame(8, $pipeline(3));
    }
}
