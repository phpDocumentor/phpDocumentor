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

use Mockery as m;

/**
 * Test case for FilesystemFactory
 * @coversDefaultClass phpDocumentor\FilesystemFactory
 */
class FilesystemFactoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var FilesystemFactory */
    private $fixture;

    /** @var m\Mock */
    private $mountManagerMock;

    /** @var m\Mock */
    private $containerMock;

    /** @var m\Mock */
    private $filesystemMock;

    /** @var Dsn */
    private $dsn;

    protected function setUp()
    {
        $this->mountManagerMock = m::mock('League\Flysystem\MountManager');
        $this->containerMock = m::mock('\DI\Container');
        $this->filesystemMock = m::mock('League\Flysystem\Filesystem');
        $this->dsn = new Dsn('testPath');

        $this->fixture = new FilesystemFactory($this->mountManagerMock, $this->containerMock);
    }

    /**
     * @covers ::__construct
     * @uses phpDocumentor\Dsn
     */
    public function testIfContainerIsRegisteredUponInstantiation()
    {
        $this->assertAttributeSame($this->containerMock, 'container', $this->fixture);
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
}
