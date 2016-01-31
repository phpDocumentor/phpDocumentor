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
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\Strategy;
use phpDocumentor\DomainModel\ConfigureCache;
use phpDocumentor\DomainModel\ConfigureCacheHandler;
use phpDocumentor\DomainModel\Uri;
use PHPUnit_Framework_TestCase;
use Stash\Pool;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\ConfigureCacheHandler
 * @covers ::__construct
 */
final class ConfigureCacheHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var m\MockInterface|Pool
     */
    private $pool;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var ConfigureCacheHandler
     */
    private $fixture;

    /**
     * @var m\MockInterface
     */
    private $configStrategy;

    protected function setUp()
    {
        $this->pool = m::mock(Pool::class);
        $this->configStrategy = m::mock(Strategy::class);
        $this->configStrategy->shouldReceive('match')->andReturn(true);

        $root = vfsStream::setup('dir');

        vfsStream::newFile('foo.xml')->at($root)->withContent('<foo></foo>');
        $uri = new Uri(vfsStream::url('dir/foo.xml'));

        $this->configurationFactory = new ConfigurationFactory([$this->configStrategy], $uri);
        $this->fixture = new ConfigureCacheHandler($this->pool, $this->configurationFactory);
    }

    /**
     * @covers ::__invoke
     * @dataProvider provideOnOffToggleForTestIfCacheCanBeDisabled
     */
    public function testSettingTheCachePathAndTogglingIfItIsEnabled($useCache)
    {
        $this->configStrategy->shouldReceive('convert')->andReturn([
            'phpdocumentor' => [
                'use-cache' => $useCache,
                'paths' => [
                    'cache' => './',
                ],
            ],
        ]);

        $this->pool->shouldReceive('flush')->times(!$useCache ? 1 : 0);
        $this->pool->shouldReceive('getDriver->setOptions')->with(['path' => './']);

        $this->fixture->__invoke(new ConfigureCache());
    }

    public function provideOnOffToggleForTestIfCacheCanBeDisabled()
    {
        return [ [true], [false] ];
    }
}
