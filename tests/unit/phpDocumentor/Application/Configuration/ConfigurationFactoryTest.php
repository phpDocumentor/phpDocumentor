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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Application\Configuration\Factory\Strategy;
use phpDocumentor\DomainModel\Uri;

/**
 * Test case for ConfigurationFactory
 *
 * @coversDefaultClass phpDocumentor\Application\Configuration\ConfigurationFactory
 * @covers ::<private>
 */
final class ConfigurationFactoryTest extends MockeryTestCase
{
    /**
     * @covers ::get
     * @expectedException \Exception
     * @expectedExceptionMessage No strategy found that matches the configuration xml
     */
    public function testItHaltsIfNoMatchingStrategyCanBeFound()
    {
        $root = vfsStream::setup('dir');

        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');
        $uri = new Uri(vfsStream::url('dir/foo.xml'));

        $configurationFactory = new ConfigurationFactory([], $uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItRegistersStrategies()
    {
        $root = vfsStream::setup('dir');

        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');
        $uri = new Uri(vfsStream::url('dir/foo.xml'));

        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('match')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class));

        $configurationFactory = new ConfigurationFactory([$strategy], $uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::replaceLocation
     */
    public function testThatTheLocationCanBeReplaced()
    {
        $cachedContent = ['test'];
        $root = vfsStream::setup('dir');
        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');

        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('match')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class))->andReturn($cachedContent);

        // Setup a prepopulated factory with path and cachedContents
        $uri = new Uri(vfsStream::url('dir/foo.xml'));
        $factory = new ConfigurationFactory([$strategy], $uri);
        $this->assertAttributeSame($uri, 'uri', $factory);
        $factory->get();
        $this->assertAttributeSame($cachedContent, 'cachedConfiguration', $factory);

        // Assert that the uri was replaced and the cache cleared
        $uri2 = new Uri(vfsStream::url('dir/foo2.xml'));
        $factory->replaceLocation($uri2);
        $this->assertAttributeSame($uri2, 'uri', $factory, 'The URI should have been replaced');
        $this->assertAttributeSame(null, 'cachedConfiguration', $factory, 'Cache should have been cleared');
    }

    /**
     * @covers ::clearCache
     */
    public function testThatTheCacheCanBeCleared()
    {
        $cachedContent = ['test'];
        $root = vfsStream::setup('dir');
        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');

        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('match')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class))->andReturn($cachedContent);

        // Setup a prepopulated factory with path and cachedContents
        $uri = new Uri(vfsStream::url('dir/foo.xml'));
        $factory = new ConfigurationFactory([$strategy], $uri);
        $this->assertAttributeSame($uri, 'uri', $factory);
        $factory->get();
        $this->assertAttributeSame($cachedContent, 'cachedConfiguration', $factory);

        $factory->clearCache();
        $this->assertAttributeSame(null, 'cachedConfiguration', $factory, 'Cache should have been cleared');
    }

    /**
     * @covers ::get
     */
    public function testThatOutputIsCached()
    {
        $cachedContent = ['test'];
        $root = vfsStream::setup('dir');
        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');

        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('match')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class))->andReturn($cachedContent);

        // Setup a prepopulated factory with path and cachedContents
        $uri = new Uri(vfsStream::url('dir/foo.xml'));
        $factory = new ConfigurationFactory([$strategy], $uri);
        $this->assertAttributeSame($uri, 'uri', $factory);

        // populate the cache
        $factory->get();
        $this->assertAttributeSame($cachedContent, 'cachedConfiguration', $factory);

        // convert method on the strategy should not be called twice (see mock)
        $factory->get();
    }

    /**
     * @covers ::__construct
     * @covers ::addMiddleware
     * @covers ::get
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
        $strategy->shouldReceive('match')->once()->with(m::type(\SimpleXMLElement::class))->andReturn(true);
        $strategy->shouldReceive('convert')->once()->with(m::type(\SimpleXMLElement::class))->andReturn($inputValue);

        // Setup a prepopulated factory with path and cachedContents
        $uri = new Uri(vfsStream::url('dir/foo.xml'));
        $factory = new ConfigurationFactory([$strategy], $uri, [$middleWare1]);
        $factory->addMiddleware($middleWare2);

        $factory->get();

        $this->assertAttributeSame($afterMiddleware2Value, 'cachedConfiguration', $factory);
    }
}
