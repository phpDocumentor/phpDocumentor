<?php

declare(strict_types=1);

namespace Application;

use phpDocumentor\Application\PipelineFactory;
use PHPUnit\Framework\TestCase;

final class PipelineFactoryTest extends TestCase
{
    public function test_creates_a_pipeline_with_the_given_series_of_stages() : void
    {
        $pipeline = PipelineFactory::create([
            static function ($value) {
                return $value + 1;
            },
            static function ($value) {
                return $value * 2;
            },
        ]);

        // can only test whether it worked by running the pipeline and
        // getting the expected output
        $this->assertSame(8, $pipeline(3));
    }
}
