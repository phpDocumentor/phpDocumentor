<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Flyfinder\Path;
use Flyfinder\Specification\InPath;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use LogicException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Dsn;
use const DIRECTORY_SEPARATOR;
use function sys_get_temp_dir;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FlySystemFactory
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemFactoryTest extends MockeryTestCase
{
    /** @var FlySystemFactory */
    private $fixture;

    /** @var m\Mock|MountManager */
    private $mountManagerMock;

    /** @var m\Mock|Filesystem */
    private $filesystemMock;

    /** @var Dsn */
    private $dsn;

    protected function setUp() : void
    {
        $this->mountManagerMock = m::mock('League\Flysystem\MountManager');
        $this->filesystemMock = m::mock(Filesystem::class);
        $this->dsn = Dsn::createFromString(sys_get_temp_dir());
        $this->fixture = new FlySystemFactory($this->mountManagerMock);
    }

    /**
     * @covers ::create
     */
    public function testCreateLocalFilesystemWithoutCache() : void
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->once();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow(LogicException::class);

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf(Filesystem::class, $result);

        /** @var AbstractAdapter $adapter */
        $adapter = $result->getAdapter();
        $pathPrefix = $adapter->getPathPrefix();
        $this->assertSame((string) $this->dsn . DIRECTORY_SEPARATOR, $pathPrefix);
    }

    /**
     * @covers ::create
     */
    public function testCreateLocalFilesystemWithCache() : void
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->never();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andReturn($this->filesystemMock);
        $this->filesystemMock->shouldReceive('addPlugin');

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf(Filesystem::class, $result);
    }

    /**
     * @covers ::create
     */
    public function testUnsupportedScheme() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->mountManagerMock->shouldReceive('mountFilesystem')->never();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow(LogicException::class);
        $dsn = Dsn::createFromString('git+http://github.com');

        $this->fixture->create($dsn);
    }

    /**
     * @covers ::create
     */
    public function testFlyFinderIsRegistered() : void
    {
        $this->mountManagerMock->shouldReceive('mountFilesystem')->once();
        $this->mountManagerMock->shouldReceive('getFilesystem')->once()->andThrow(LogicException::class);
        $fileSystem = $this->fixture->create($this->dsn);

        $fileSystem->find(new InPath(new Path('a')));
    }
}
