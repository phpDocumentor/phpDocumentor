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
use \Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Application\Console\Command\Helper\ConfigurationHelper;
use phpDocumentor\Application\Console\Command\Helper\LoggerHelper;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\DomainModel\Parser\FileCollector;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\I18n\Translator\Translator;

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
    public function testDefaultTargetDirCreation()
    {
        $this->markTestIncomplete('Needs to be updated, to new command');
        $input = new ArrayInput([]);
        $output = new DummyOutput();

        $cache = m::mock(Memory::class);
        $cache->shouldReceive('getOptions->setCacheDir')->with(m::type('string'));
        $cache->shouldReceive('getOptions->getCacheDir')->andReturn(sys_get_temp_dir());
        $cache->shouldReceive('getOptions->getReadable')->andReturn(true);
        $cache->shouldReceive('getOptions->getWritable')->andReturn(true);
        $cache->shouldReceive('getOptions->getKeyPattern')->andReturn('/.+/');
        $cache->shouldReceive('getOptions->getNamespace')->andReturn('PhpDoc\\Cache');
        $cache->shouldDeferMissing();

        $translator = m::mock(Translator::class);
        $translator->shouldReceive('translate')
            ->zeroOrMoreTimes();

        $parser = m::mock(Parser::class);
        $parser->shouldReceive(
            'setForced',
            'setEncoding',
            'setMarkers',
            'setIgnoredTags',
            'setValidate',
            'setDefaultPackageName',
            'setPath',
            'parse'
        );

        $projectDescriptorBuilder = m::mock(ProjectDescriptorBuilder::class);
        $projectDescriptorBuilder->shouldReceive('createProjectDescriptor', 'getProjectDescriptor')
            ->andReturn(new ProjectDescriptor('test'));

        $command = new ParseCommand(
            m::mock(PipelineInterface::class),
            m::mock(\phpDocumentor\Translator\Translator::class)
        );

        $command->run($input, $output);
    }
}
