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

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Renderer\Asset
 * @covers ::<private>
 */
final class AssetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::content
     */
    public function itRegistersTheContentOfAnAsset()
    {
        $content = 'content';

        $asset = new Asset($content);

        $this->assertSame($content, $asset->content());
    }
}
