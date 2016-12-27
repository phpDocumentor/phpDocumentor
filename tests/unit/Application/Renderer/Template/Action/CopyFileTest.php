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
use phpDocumentor\DomainModel\Renderer\Artefacts;
use phpDocumentor\DomainModel\Renderer\Assets;
use phpDocumentor\DomainModel\Renderer\RenderContext;
use phpDocumentor\DomainModel\Renderer\Template\Parameter;

/**
 * @coversDefaultClass phpDocumentor\Application\Renderer\Template\Action\CopyFile
 * @covers ::<private>
 */
final class CopyFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::create
     * @covers ::getRenderContext
     * @covers ::getSource
     * @covers ::getDestination
     */
    public function itShouldCreateNewCommandWithContextSourceAndDestinationFromTemplateParameters()
    {
        $renderContext = new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class));
        $source = 'asset.jpg';
        $destination = 'images/asset.jpg';

        $action = CopyFile::create([
            'renderContext' => new Parameter('renderContext', $renderContext),
            'source' => new Parameter('source', $source),
            'destination' => new Parameter('destination', $destination)
        ]);

        $this->assertInstanceOf(CopyFile::class, $action);
        $this->assertSame($renderContext, $action->getRenderContext());
        $this->assertTrue($action->getSource()->equals(new Path($source)));
        $this->assertTrue($action->getDestination()->equals(new Path($destination)));
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function itShouldReturnALogLineWhenCastAsString()
    {
        $renderContext = new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class));
        $source = 'asset.jpg';
        $destination = 'images/asset.jpg';

        $action = CopyFile::create([
            'renderContext' => new Parameter('renderContext', $renderContext),
            'source' => new Parameter('source', $source),
            'destination' => new Parameter('destination', $destination)
        ]);

        $this->assertSame('Copied file asset.jpg to images/asset.jpg', (string)$action);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatThePassedOptionsAreAllParameterObjects()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'renderContext' => new Parameter(
                'renderContext',
                new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class))
            ),
            'source' => new Parameter('source', 'asset.jpg'),
            'destination' => 'images/asset.jpg' // introduce a mistake here
        ]);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatTheRenderContextExists()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'source' => new Parameter('source', 'asset.jpg'),
            'destination' => new Parameter('destination', 'images/asset.jpg')
        ]);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatTheSourceParameterExists()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'renderContext' => new Parameter(
                'renderContext',
                new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class))
            ),
            'destination' => new Parameter('destination', 'images/asset.jpg')
        ]);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatTheDestinationParameterExists()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'renderContext' => new Parameter(
                'renderContext',
                new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class))
            ),
            'source' => new Parameter('source', 'asset.jpg'),
        ]);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatTheSourceIsAString()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'renderContext' => new Parameter(
                'renderContext',
                new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class))
            ),
            'source' => new Parameter('source', []),
            'destination' => new Parameter('destination', 'images/asset.jpg')
        ]);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatTheRenderContextIsAValidRenderContextObject()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'renderContext' => new Parameter('renderContext', 'invalidRenderContext'),
            'source' => new Parameter('source', 'asset.jpg'),
            'destination' => new Parameter('destination', 'images/asset.jpg')
        ]);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldCheckThatTheDestinationIsAString()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create([
            'renderContext' => new Parameter(
                'renderContext',
                new RenderContext(new ReadModels([]), m::mock(Assets::class), m::mock(Artefacts::class))
            ),
            'source' => new Parameter('source', 'asset.jpg'),
            'destination' => new Parameter('destination', [])
        ]);
    }
}
