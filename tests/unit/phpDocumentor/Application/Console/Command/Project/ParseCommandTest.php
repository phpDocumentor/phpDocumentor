<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Console\Command\Project;

use League\Pipeline\PipelineInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @coversDefaultClass phpDocumentor\Application\Console\Command\Project\ParseCommand
 * @covers ::<protected>
 */
class ParseCommandTest extends MockeryTestCase
{
    /**
     * Tests the processing of target directory when non is provided.
     * @covers ::execute
     */
    public function testPipelineIsInvokedWithTheNecessaryOptions()
    {
        $input = new StringInput('--force -f abc');
        $output = new DummyOutput();

        $pipeline = m::mock(PipelineInterface::class);
        $pipeline
            ->shouldReceive('__invoke')
            ->withArgs(function(array $options){
                return $options['force'] === true && $options['filename'] === ['abc'];
            })
            ->once();

        $command = new ParseCommand($pipeline);

        $this->assertSame(0, $command->run($input, $output));
    }
}
