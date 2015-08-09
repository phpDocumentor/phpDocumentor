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

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Cache;
use Mockery as m;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\CacheProjectHandler
 */
class CacheProjectHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analyzer|m\MockInterface */
    private $analyzer;

    /** @var Cache|m\MockInterface */
    private $cache;

    /** @var CacheProjectHandler */
    private $fixture;

    public function setUp()
    {
        $this->analyzer = m::mock(Analyzer::class);
        $this->cache   = m::mock(Cache::class);
        $this->fixture = new CacheProjectHandler($this->cache, $this->analyzer);
    }

    /**
     * @covers ::__construct
     * @covers ::__invoke
     * @uses phpDocumentor\Descriptor\ProjectDescriptor
     * @uses Desarrolla2\Cache\Adapter\File
     * @uses phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper
     */
    public function testProjectGetsCached()
    {
        $this->markTestIncomplete('Shall be rewritten after new reflection integration.');
        $target = 'target';
        $projectDescriptor = new ProjectDescriptor('');

        $this->analyzer->shouldReceive('getProjectDescriptor')->andReturn($projectDescriptor);

        $this->cache->shouldReceive('setAdapter')->with(m::type(AdapterInterface::class));
        $this->cache->shouldReceive('set')->with('settings', $projectDescriptor->getSettings())->once();
        $this->cache->shouldReceive('set')->with('files', $projectDescriptor->getFiles())->once();

        $this->fixture->__invoke(new CacheProject($target));

        $this->assertTrue(true);
    }
}
