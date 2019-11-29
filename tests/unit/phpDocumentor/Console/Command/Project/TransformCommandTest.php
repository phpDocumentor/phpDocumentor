<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Command\Project;

use League\Pipeline\PipelineInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @coversDefaultClass \phpDocumentor\Console\Command\Project\TransformCommand
 * @covers ::<protected>
 */
class TransformCommandTest extends MockeryTestCase
{
    /**
     * Tests the processing of target directory when non is provided.
     *
     * @covers ::execute
     */
    public function testPipelineIsInvokedWithTheNecessaryOptions() : void
    {
        $input  = new StringInput('-t abc');
        $output = new BufferedOutput();

        $pipeline = m::mock(PipelineInterface::class);
        $pipeline
            ->shouldReceive('__invoke')
            ->withArgs(static function (array $options) {
                return $options['target'] === 'abc';
            })
            ->once();

        $command = new TransformCommand($pipeline);

        $this->assertSame(0, $command->run($input, $output));
    }
}
