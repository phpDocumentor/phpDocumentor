<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Pipeline;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\PipelineFactory
 * @covers ::<private>
 */
final class PipelineFactoryTest extends TestCase
{
    /**
     * @covers ::create
     */
    public function test_creates_a_pipeline_with_the_given_series_of_stages() : void
    {
        $pipeline = PipelineFactory::create(
            [
                static function ($value) {
                    return $value + 1;
                },
                static function ($value) {
                    return $value * 2;
                },
            ]
        );

        // can only test whether it worked by running the pipeline and
        // getting the expected output
        $this->assertSame(8, $pipeline(3));
    }
}
