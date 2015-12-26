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

namespace phpDocumentor;

use Flyfinder\Specification\InPath;
use Mockery as m;

/**
 * Test case for FilesystemFactory
 * @coversDefaultClass phpDocumentor\FlySystemFactory
 */
class FlySystemFactoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var FlySystemFactory */
    private $fixture;

    /** @var m\Mock */
    private $mountManagerMock;

    /** @var m\Mock */
    private $filesystemMock;

    /** @var Dsn */
    private $dsn;

    protected function setUp()
    {
        $this->mountManagerMock = m::mock('League\Flysystem\MountManager');
        $this->filesystemMock = m::mock('League\Flysystem\Filesystem');
        $this->dsn = new Dsn('file:///tmp');
        $this->fixture = new FlySystemFactory($this->mountManagerMock);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     * @uses phpDocumentor\Path
     * @uses phpDocumentor\Dsn
     */
    public function testCreateLocalFilesystemWithoutCache()
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->once();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow('\LogicException');

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf('League\Flysystem\Filesystem', $result);

        $adapter = $result->getAdapter();
        $pathPrefix = $adapter->getPathPrefix();
        $this->assertEquals(realpath('/tmp').'/', $pathPrefix);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     * @uses phpDocumentor\Path
     * @uses phpDocumentor\Dsn
     */
    public function testCreateLocalFilesystemWithCache()
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->never();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andReturn($this->filesystemMock);
        $this->filesystemMock->shouldReceive('addPlugin');

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf('League\Flysystem\Filesystem', $result);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     * @uses phpDocumentor\Dsn
     * @expectedException \InvalidArgumentException
     */
    public function testUnsupportedScheme()
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->never();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow('\LogicException');
        $dsn = new Dsn('git+http://github.com');

        $this->fixture->create($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     * @uses phpDocumentor\Path
     * @uses phpDocumentor\Dsn
     */
    public function testFlyFinderIsRegistered()
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->once();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow('\LogicException');
        $fileSystem = $this->fixture->create($this->dsn);

        $fileSystem->find(new InPath(new \Flyfinder\Path('a')));
    }
}
