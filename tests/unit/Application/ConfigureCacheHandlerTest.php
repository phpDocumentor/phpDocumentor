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

namespace phpDocumentor\Application;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\Strategy;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Uri;
use PHPUnit_Framework_TestCase;
use Stash\Pool;

/**
 * @coversDefaultClass phpDocumentor\Application\ConfigureCacheHandler
 * @covers ::__construct
 */
final class ConfigureCacheHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var m\MockInterface|Pool */
    private $pool;

    /** @var ConfigureCacheHandler */
    private $fixture;

    protected function setUp()
    {
        $this->pool = m::mock(Pool::class);
        $this->fixture = new ConfigureCacheHandler($this->pool);
    }

    /**
     * @covers ::__invoke
     * @dataProvider provideOnOffToggleForTestIfCacheCanBeDisabled
     */
    public function testSettingTheCachePathAndTogglingIfItIsEnabled($useCache)
    {
        $path = './';
        $this->pool->shouldReceive('flush')->times(!$useCache ? 1 : 0);
        $this->pool->shouldReceive('getDriver->setOptions')->with(['path' => $path]);

        $this->fixture->__invoke(new ConfigureCache(new Path($path), $useCache));
    }

    public function provideOnOffToggleForTestIfCacheCanBeDisabled()
    {
        return [ [true], [false] ];
    }
}
