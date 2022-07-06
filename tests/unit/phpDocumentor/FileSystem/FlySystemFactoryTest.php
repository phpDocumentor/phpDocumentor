<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\FileSystem;

use Flyfinder\Path;
use Flyfinder\Specification\InPath;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use LogicException;
use phpDocumentor\Dsn;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function substr;
use function sys_get_temp_dir;

use const DIRECTORY_SEPARATOR;
use const PHP_OS_FAMILY;

/**
 * @coversDefaultClass \phpDocumentor\FileSystem\FlySystemFactory
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemFactoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var FlySystemFactory */
    private $fixture;

    /** @var ObjectProphecy|MountManager */
    private $mountManagerMock;

    /** @var ObjectProphecy|Filesystem */
    private $filesystemMock;

    /** @var Dsn */
    private $dsn;

    protected function setUp(): void
    {
        $this->mountManagerMock = $this->prophesize(MountManager::class);
        $this->filesystemMock = $this->prophesize(Filesystem::class);
        $this->dsn = Dsn::createFromString(sys_get_temp_dir());
        $this->fixture = new FlySystemFactory($this->mountManagerMock->reveal());
    }

    /**
     * @covers ::create
     */
    public function testCreateLocalFilesystemWithoutCache(): void
    {
        $this->mountManagerMock->mountFilesystem(Argument::any(), Argument::any())->shouldBeCalledOnce();
        $this->mountManagerMock->getFilesystem(Argument::any())
            ->shouldBeCalledOnce()
            ->willThrow(LogicException::class);

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf(Filesystem::class, $result);

        /** @var AbstractAdapter $adapter */
        $adapter = $result->getAdapter();
        $pathPrefix = $adapter->getPathPrefix();

        $expected = $this->formatOsSpecificResult();

        $this->assertSame($expected . DIRECTORY_SEPARATOR, $pathPrefix);
    }

    /**
     * @covers ::create
     */
    public function testCreateLocalFilesystemWithCache(): void
    {
        $this->filesystemMock->addPlugin(Argument::any())->shouldBeCalled();
        $this->mountManagerMock->mountFilesystem(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->mountManagerMock->getFilesystem(Argument::any())
            ->shouldBeCalledOnce()
            ->willReturn($this->filesystemMock->reveal());

        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf(Filesystem::class, $result);
    }

    /**
     * @covers ::create
     */
    public function testUnsupportedScheme(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->mountManagerMock->mountFilesystem(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->mountManagerMock->getFilesystem(Argument::any())
            ->shouldBeCalledOnce()
            ->willThrow(LogicException::class);
        $dsn = Dsn::createFromString('git+http://github.com');

        $this->fixture->create($dsn);
    }

    /**
     * @covers ::create
     */
    public function testFlyFinderIsRegistered(): void
    {
        $this->mountManagerMock->mountFilesystem(Argument::any(), Argument::any())->shouldBeCalledOnce();
        $this->mountManagerMock->getFilesystem(Argument::any())
            ->shouldBeCalledOnce()
            ->willThrow(LogicException::class);
        $fileSystem = $this->fixture->create($this->dsn);

        $fileSystem->find(new InPath(new Path('a')));
    }

    /**
     * @see FlySystemFactory::stripScheme
     */
    private function formatOsSpecificResult(): string
    {
        $expected = (string) $this->dsn;
        if (PHP_OS_FAMILY === 'Windows') {
            $expected = substr((string) $this->dsn, 8);
            if ($expected === false) {
                $this->fail('dsn is not valid');
            }
        }

        return $expected;
    }
}
