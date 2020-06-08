<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Console\Command\Project;

use League\Pipeline\PipelineInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Console\Application;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use function array_keys;

/**
 * @coversDefaultClass \phpDocumentor\Console\Command\Project\RunCommand
 * @covers ::__construct
 * @covers ::<private>
 */
class RunCommandTest extends MockeryTestCase
{
    /**
     * @covers ::execute
     */
    public function testPipelineIsInvokedWithTheNecessaryOptions() : void
    {
        $input = new StringInput('--force -f abc');
        $output = new BufferedOutput();

        $pipeline = m::mock(PipelineInterface::class);
        $pipeline
            ->shouldReceive('__invoke')
            ->withArgs(
                static function (array $options) {
                    return $options['force'] === true && $options['filename'] === ['abc'];
                }
            )
            ->once();

        $command = new RunCommand(m::mock(ProjectDescriptorBuilder::class), $pipeline);
        $application = m::mock(Application::class);
        $application->shouldReceive('getVersion')->andReturn('3.0');
        $application->shouldReceive('getHelperSet')->andReturn(new HelperSet());
        $application->shouldReceive('getDefinition')->andReturn(new InputDefinition());
        $command->setApplication($application);

        $this->assertSame(0, $command->run($input, $output));
    }

    /**
     * @covers ::configure
     */
    public function testCommandIsConfiguredWithTheRightOptions() : void
    {
        $pipeline = m::mock(PipelineInterface::class);
        $command = new RunCommand(m::mock(ProjectDescriptorBuilder::class), $pipeline);
        $options = $command->getDefinition()->getOptions();
        $this->assertEquals(
            [
                'target',
                'cache-folder',
                'filename',
                'directory',
                'encoding',
                'extensions',
                'ignore',
                'ignore-tags',
                'hidden',
                'ignore-symlinks',
                'markers',
                'title',
                'force',
                'validate',
                'visibility',
                'defaultpackagename',
                'sourcecode',
                'template',
                'setting',
                'list-settings',
                'parseprivate',
            ],
            array_keys($options)
        );
    }
}
