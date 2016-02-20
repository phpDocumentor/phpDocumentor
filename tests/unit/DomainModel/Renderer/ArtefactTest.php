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

use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Renderer\Artefact
 * @covers ::<private>
 */
final class ArtefactTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::location
     * @covers ::content
     */
    public function itRegistersTheLocationAndContentOfAnArtefact()
    {
        $location = new Path('.');
        $content = 'content';

        $artefact = new Artefact($location, $content);

        $this->assertSame($location, $artefact->location());
        $this->assertSame($content, $artefact->content());
    }
}
