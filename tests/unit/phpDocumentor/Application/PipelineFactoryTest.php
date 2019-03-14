<?php

declare(strict_types=1);

namespace Application;

use phpDocumentor\Application\PipelineFactory;

final class PipelineFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function test_creates_a_pipeline_with_the_given_series_of_stages(): void
    {
        $pipeline = PipelineFactory::create([
            function ($value) {
                return $value + 1;
            },
            function ($value) {
                return $value * 2;
            }
        ]);

        // can only test whether it worked by running the pipeline and
        // getting the expected output
        $this->assertSame(8, $pipeline(3));
    }
}
