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

namespace phpDocumentor\Application\Renderer\Template\Action;

use Mockery as m;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\ReadModel\ReadModels;
use phpDocumentor\DomainModel\Renderer\Artefact;
use phpDocumentor\DomainModel\Renderer\Artefacts;
use phpDocumentor\DomainModel\Renderer\Asset;
use phpDocumentor\DomainModel\Renderer\AssetNotFoundException;
use phpDocumentor\DomainModel\Renderer\Assets;
use phpDocumentor\DomainModel\Renderer\RenderContext;
use phpDocumentor\DomainModel\Renderer\Template\Parameter;

/**
 * @coversDefaultClass phpDocumentor\Application\Renderer\Template\Action\CopyFileHandler
 * @covers ::<private>
 */
final class CopyFileHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CopyFileHandler */
    private $handler;

    public function setUp()
    {
        $this->handler = new CopyFileHandler();
    }

    /**
     * @test
     * @covers ::__invoke
     */
    public function itShouldStoreAnAssetAsArtefact()
    {
        $assets = m::mock(Assets::class);
        $artefacts = m::mock(Artefacts::class);

        $renderContext = new RenderContext(new ReadModels([]), $assets, $artefacts);
        $source = 'asset.jpg';
        $destination = 'images/asset.jpg';

        $action = $this->givenAnActionWith($renderContext, $source, $destination);
        $assets->shouldReceive('get')->once()
            ->with(
                m::on(
                    function (Path $value) use ($source) {
                        return $value->equals(new Path($source));
                    }
                )
            )
            ->andReturn(new Asset('data'));

        $artefacts->shouldReceive('persist')->once()
            ->with(
                m::on(
                    function (Artefact $value) use ($destination) {
                        $this->assertTrue($value->location()->equals(new Path($destination)));
                        $this->assertTrue($value->content() === 'data');
                        return true;
                    }
                )
            );

        $this->handler->__invoke($action);
    }

    /**
     * @test
     * @covers ::__invoke
     */
    public function itShouldStoreTheContentsOfAnAssetFolderAsArtefacts()
    {
        $assets = m::mock(Assets::class);
        $artefacts = m::mock(Artefacts::class);

        $renderContext = new RenderContext(new ReadModels([]), $assets, $artefacts);
        $source = 'images';
        $assetLocationInFolder = new Path('images/asset.jpg');
        $destination = 'img';

        $action = $this->givenAnActionWith($renderContext, $source, $destination);
        $assets->shouldReceive('get')->once()
            ->with(
                m::on(
                    function (Path $value) use ($source) {
                        return $value->equals(new Path($source));
                    }
                )
            )
            ->andReturn(new Asset\Folder(new Path('images'), [$assetLocationInFolder]));

        $assets->shouldReceive('get')->once()
            ->with(
                m::on(
                    function (Path $value) use ($assetLocationInFolder) {
                        return $value->equals($assetLocationInFolder);
                    }
                )
            )
            ->andReturn(new Asset('data'));

        $artefacts->shouldReceive('persist')->once()
            ->with(
                m::on(
                    function (Artefact $value) use ($destination) {
                        $this->assertTrue($value->location()->equals(new Path($destination . '/asset.jpg')));
                        $this->assertTrue($value->content() === 'data');
                        return true;
                    }
                )
            );

        $this->handler->__invoke($action);
    }

    /**
     * @test
     * @covers ::__invoke
     */
    public function itShouldErrorIfTheAssetCouldNotBeFound()
    {
        $this->setExpectedException(AssetNotFoundException::class);

        $assets = m::mock(Assets::class);
        $artefacts = m::mock(Artefacts::class);

        $renderContext = new RenderContext(new ReadModels([]), $assets, $artefacts);
        $action = $this->givenAnActionWith($renderContext, 'asset.jpg', 'images/asset.jpg');
        $assets->shouldReceive('get')->once()->andThrow(AssetNotFoundException::class);
        $artefacts->shouldReceive('persist')->never();

        $this->handler->__invoke($action);
    }

    /**
     * @param $renderContext
     * @param $source
     * @param $destination
     *
     * @return static
     */
    private function givenAnActionWith($renderContext, $source, $destination)
    {
        return CopyFile::create([
            'renderContext' => new Parameter('renderContext', $renderContext),
            'source' => new Parameter('source', $source),
            'destination' => new Parameter('destination', $destination)
        ]);
    }
}
