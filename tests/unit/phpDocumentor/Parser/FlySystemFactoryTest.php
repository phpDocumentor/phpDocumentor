<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Flyfinder\Path;
use Flyfinder\Specification\InPath;
use League\Flysystem\Adapter\AbstractAdapter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Dsn;
use function sys_get_temp_dir;
use const DIRECTORY_SEPARATOR;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FlySystemFactory
 */
final class FlySystemFactoryTest extends MockeryTestCase
{
    /** @var FlySystemFactory */
    private $fixture;

    /** @var m\Mock */
    private $mountManagerMock;

    /** @var m\Mock */
    private $filesystemMock;

    /** @var Dsn */
    private $dsn;

    protected function setUp() : void
    {
        $this->mountManagerMock = m::mock('League\Flysystem\MountManager');
        $this->filesystemMock = m::mock('League\Flysystem\Filesystem');
        $this->dsn = new Dsn('file://' . sys_get_temp_dir());
        $this->fixture = new FlySystemFactory($this->mountManagerMock);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     */
    public function testCreateLocalFilesystemWithoutCache() : void
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->once();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow('\LogicException');

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf('League\Flysystem\Filesystem', $result);

        /** @var AbstractAdapter $adapter */
        $adapter = $result->getAdapter();
        $pathPrefix = $adapter->getPathPrefix();
        $this->assertEquals(sys_get_temp_dir() . DIRECTORY_SEPARATOR, $pathPrefix);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     */
    public function testCreateLocalFilesystemWithCache() : void
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
     */
    public function testUnsupportedScheme() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->mountManagerMock->shouldReceive('mountFilesystem')->never();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow('\LogicException');
        $dsn = new Dsn('git+http://github.com');

        $this->fixture->create($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::<private>
     */
    public function testFlyFinderIsRegistered() : void
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->once();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow('\LogicException');
        $fileSystem = $this->fixture->create($this->dsn);

        $fileSystem->find(new InPath(new Path('a')));
    }
}
