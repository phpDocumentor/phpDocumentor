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

namespace phpDocumentor\DomainModel\Renderer;

use Mockery as m;
use phpDocumentor\DomainModel\ReadModel\ReadModels;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Renderer\RenderContext
 * @covers ::<private>
 */
final class RenderContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::readModels
     * @covers ::assets
     * @covers ::artefacts
     */
    public function itRegistersTheArtefactsAssetsAndReadModels()
    {
        $readModels = new ReadModels([]);
        $assets = m::mock(Assets::class);
        $artefacts = m::mock(Artefacts::class);

        $renderContext = new RenderContext($readModels, $assets, $artefacts);

        $this->assertSame($readModels, $renderContext->readModels());
        $this->assertSame($assets, $renderContext->assets());
        $this->assertSame($artefacts, $renderContext->artefacts());
    }
}
