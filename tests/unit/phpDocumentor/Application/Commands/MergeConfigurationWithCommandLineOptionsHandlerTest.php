<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use Mockery as m;
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\CommandlineOptionsMiddleware;
use phpDocumentor\DomainModel\MergeConfigurationWithCommandLineOptions;
use phpDocumentor\DomainModel\MergeConfigurationWithCommandLineOptionsHandler;
use phpDocumentor\DomainModel\Uri;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\MergeConfigurationWithCommandLineOptionsHandler
 * @covers ::__construct
 * @covers ::<private>
 */
final class MergeConfigurationWithCommandLineOptionsHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $handler;

    private $configurationFactory;

    private $commandlineOptionsMiddleware;

    public function setUp()
    {
        $this->configurationFactory = m::mock(ConfigurationFactory::class);
        $this->commandlineOptionsMiddleware = new CommandlineOptionsMiddleware();

        $this->handler = new MergeConfigurationWithCommandLineOptionsHandler(
            $this->configurationFactory,
            $this->commandlineOptionsMiddleware
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testItMergesConfigurationWithProvidedOptions()
    {
        $this->configurationFactory->shouldReceive('clearCache')->once();

        $this->handler->__invoke(new MergeConfigurationWithCommandLineOptions(['test']));

        $this->assertAttributeSame(['test'], 'options', $this->commandlineOptionsMiddleware);
    }

    /**
     * @covers ::__invoke
     */
    public function testOverrideConfigurationLocationWhenPassingConfigOption()
    {
        $configLocation = sys_get_temp_dir();
        $this->configurationFactory
            ->shouldReceive('replaceLocation')->once()
            ->with(m::on(function ($uri) use ($configLocation) {
                $this->assertInstanceOf(Uri::class, $uri);
                $this->assertSame((string)$uri, 'file://' . realpath($configLocation));
                return true;
            }));
        $this->configurationFactory->shouldReceive('clearCache')->once();

        $options = ['test' => 1, 'config' => $configLocation];
        $this->handler->__invoke(new MergeConfigurationWithCommandLineOptions($options));

        $this->assertAttributeSame($options, 'options', $this->commandlineOptionsMiddleware);
    }
}
