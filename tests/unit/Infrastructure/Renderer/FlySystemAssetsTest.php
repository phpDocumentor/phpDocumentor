<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Renderer;

use League\Flysystem\AdapterInterface;
use League\Flysystem\File;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Asset;
use phpDocumentor\DomainModel\Renderer\AssetNotFoundException;

/**
 * @coversDefaultClass phpDocumentor\Infrastructure\Renderer\FlySystemAssets
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemAssetsTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemInterface|m\MockInterface */
    private $filesystem;

    /** @var FlySystemAssets */
    private $assets;

    public function setUp()
    {
        $this->filesystem = m::mock(FilesystemInterface::class);

        $this->assets = new FlySystemAssets($this->filesystem);
    }

    /**
     * @test
     * @covers ::get
     */
    public function itShouldReturnAnAsset()
    {
        $location = new Path('image.jpg');
        $file = m::mock(File::class);
        $file->shouldReceive('isDir')->andReturn(false);
        $file->shouldReceive('read')->andReturn('data');

        $this->filesystem->shouldReceive('has')->andReturn(true);
        $this->filesystem->shouldReceive('get')->andReturn($file);

        $asset = $this->assets->get($location);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('data', $asset->content());
    }

    /**
     * @test
     * @covers ::get
     */
    public function itShouldReturnAFolder()
    {
        $location = new Path('images');
        $file = m::mock(File::class);
        $file->shouldReceive('isDir')->andReturn(true);

        $this->filesystem->shouldReceive('has')->andReturn(true);
        $this->filesystem->shouldReceive('get')->andReturn($file);
        $this->filesystem->shouldReceive('listContents')
            ->with((string)$location, true)
            ->andReturn([['type' => 'file', 'path' => 'images/image.jpg']]);

        $asset = $this->assets->get($location);

        $this->assertInstanceOf(Asset\Folder::class, $asset);
        $this->assertEquals([new Path('images/image.jpg')], $asset->getArrayCopy());
    }

    /**
     * @test
     * @covers ::get
     */
    public function itShouldNotReturnSubFoldersWhenRetrievingAFolder()
    {
        $location = new Path('images');
        $file = m::mock(File::class);
        $file->shouldReceive('isDir')->andReturn(true);

        $this->filesystem->shouldReceive('has')->andReturn(true);
        $this->filesystem->shouldReceive('get')->andReturn($file);
        $this->filesystem->shouldReceive('listContents')
            ->with((string)$location, true)
            ->andReturn(
                [
                    ['type' => 'dir', 'path' => 'images/cats'],
                    ['type' => 'file', 'path' => 'images/cats/cute.png'],
                    ['type' => 'file', 'path' => 'images/image.jpg']
                ]
            );

        $asset = $this->assets->get($location);

        $this->assertInstanceOf(Asset\Folder::class, $asset);
        $this->assertCount(2, $asset->getArrayCopy());
        $this->assertEquals(new Path('images/cats/cute.png'), $asset->getArrayCopy()[0]);
        $this->assertEquals(new Path('images/image.jpg'), $asset->getArrayCopy()[1]);
    }

    /**
     * @test
     * @covers ::get
     */
    public function itShouldErrorWhenFetchingAnAssetAndItDoesntExist()
    {
        $this->setExpectedException(AssetNotFoundException::class);
        $this->filesystem->shouldReceive('has')->andReturn(false);
        $this->filesystem->shouldReceive('get')->never();

        $this->assets->get(new Path('image.jpg'));
    }

    /**
     * @test
     * @covers ::has
     */
    public function itShouldCheckWithFlysystemIfAnAssetExists()
    {
        $this->filesystem->shouldReceive('has')->with('image.jpg')->andReturn(true);
        $this->filesystem->shouldReceive('has')->with('does-not-exist.jpg')->andReturn(false);

        $this->assertTrue($this->assets->has(new Path('image.jpg')));
        $this->assertFalse($this->assets->has(new Path('does-not-exist.jpg')));
    }
}
