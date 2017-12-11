<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\phpDocumentor\Parser\Command\Project;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Command\Helper\LoggerHelper;
use phpDocumentor\Console\Output\Output;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\DomainModel\Parser\FileCollector;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Command\Project\ParseCommand;
use \Mockery as m;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Zend\Cache\Storage\StorageInterface;
use Zend\I18n\Translator\Translator;

/**
 * @coversDefaultClass phpDocumentor\Parser\Command\Project\ParseCommand
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
        $input = new ArrayInput([]);
        $output = new Output();

        $configurationHelper = m::mock(ConfigurationHelper::class);
        $configurationHelper->shouldReceive('getName')
            ->andReturn('phpdocumentor_configuration');
        $configurationHelper->shouldReceive('setHelperSet');
        $configurationHelper->shouldReceive('getOption')
            ->with($input, 'extensions', 'parser/extensions', array('php', 'php3', 'phtml'), true)
            ->andReturn([]);
        $configurationHelper->shouldReceive('getOption')
            ->with($input, 'target', 'parser/target');
        $configurationHelper->shouldReceive('getOption')
            ->with($input, m::any(), m::any(), m::any(), m::any())
            ->andReturn([]);
        $configurationHelper->shouldReceive('getOption')
            ->with($input, m::any(), m::any(), m::any())
            ->andReturn([]);
        $configurationHelper->shouldReceive('getOption')
            ->with($input, m::any(), m::any())
            ->andReturn('Title');
        $configurationHelper->shouldReceive('getConfigValueFromPath')
            ->andReturn([]);

        $loggerHelper = m::mock(LoggerHelper::class);
        $loggerHelper->shouldReceive('getName')
            ->andReturn('phpdocumentor_logger');
        $loggerHelper->shouldReceive('setHelperSet');
        $loggerHelper->shouldReceive('addOptions');
        $loggerHelper->shouldReceive('connectOutputToLogging');

        $translator = m::mock(Translator::class);
        $translator->shouldReceive('translate')
            ->zeroOrMoreTimes();

        $cache = m::mock(StorageInterface::class);
        $cache->shouldReceive('getOptions')
            ->twice()
            ->andReturnSelf();
        $cache->shouldReceive('setCacheDir');
        $cache->shouldReceive('getCacheDir');
        $cache->shouldReceive('getItem');
        $cache->shouldReceive('setItem');
        $cache->shouldReceive('getIterator')
        ->andReturn(new \ArrayIterator());

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
            $projectDescriptorBuilder,
            $parser,
            m::mock(FileCollector::class),
            $translator,
            $cache,
            new ExampleFinder(),
            m::mock(\phpDocumentor\Partials\Collection::class)
        );



        $command->setHelperSet(
            new HelperSet(
                [
                    $configurationHelper,
                    $loggerHelper
                ]
            )
        );

        $command->run($input, $output);
    }
}
