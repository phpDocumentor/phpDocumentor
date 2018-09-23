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

namespace phpDocumentor\Application\Configuration;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Application\Configuration\Factory\Strategy;
use phpDocumentor\Application\Configuration\Factory\Version3;
use phpDocumentor\DomainModel\Uri;

/**
 * Test case for ConfigurationFactory
 *
 * @coversDefaultClass \phpDocumentor\Application\Configuration\ConfigurationFactory
 * @covers ::<private>
 */
final class ConfigurationFactoryTest extends MockeryTestCase
{
    /**
     * @covers ::fromUri
     * @expectedException \Exception
     * @expectedExceptionMessage No strategy found that matches the configuration xml
     */
    public function testItHaltsIfNoMatchingStrategyCanBeFound()
    {
        $root = vfsStream::setup('dir');

        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');
        $uri = new Uri(vfsStream::url('dir/foo.xml'));

        $configurationFactory = new ConfigurationFactory([]);
        $configurationFactory->fromUri($uri);
    }

    /**
     * @covers ::__construct
     * @covers ::fromUri
     * @covers ::<private>
     */
    public function testItRegistersStrategies()
    {
        $root = vfsStream::setup('dir');

        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');
        $uri = new Uri(vfsStream::url('dir/foo.xml'));

        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('supports')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class));

        $configurationFactory = new ConfigurationFactory([$strategy]);
        $configurationFactory->fromUri($uri);
    }

    /**
     * @covers ::__construct
     * @covers ::addMiddleware
     * @covers ::fromUri
     */
    public function testThatMiddlewaresAreAddedAndApplied()
    {
        $inputValue = ['test'];
        $afterMiddleware1Value = ['test', 'test2'];
        $afterMiddleware2Value = ['test', 'test2', 'test3'];

        $root = vfsStream::setup('dir');
        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');

        $middleWare1 = function ($value) use ($inputValue, $afterMiddleware1Value) {
            $this->assertSame($inputValue, $value);

            return $afterMiddleware1Value;
        };

        $middleWare2 = function ($value) use ($afterMiddleware1Value, $afterMiddleware2Value) {
            $this->assertSame($afterMiddleware1Value, $value);

            return $afterMiddleware2Value;
        };

        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('supports')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class))->andReturn($inputValue);

        // Setup a prepopulated factory with path and cachedContents
        $uri = new Uri(vfsStream::url('dir/foo.xml'));
        $factory = new ConfigurationFactory([$strategy], [$middleWare1]);
        $factory->addMiddleware($middleWare2);

        $data = $factory->fromUri($uri);

        $this->assertEquals($afterMiddleware2Value, $data->getArrayCopy());
    }
}
