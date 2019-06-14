<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Parser;

use League\Flysystem\MountManager;
use phpDocumentor\Dsn;
use phpDocumentor\Infrastructure\FlySystemFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Infrastructure\Parser\FlySystemCollector
 */
class FlySystemCollectorTest extends TestCase
{
    /**
     * @covers ::getFiles()
     * @uses \phpDocumentor\Infrastructure\Parser\SpecificationFactory
     * @uses \phpDocumentor\Infrastructure\FlySystemFactory
     */
    public function testSingleSourceDir()
    {
        $fileCollector = new FlySystemCollector(
            new SpecificationFactory(),
            new FlySystemFactory(new MountManager())
        );

        $files = $fileCollector->getFiles(new Dsn('file://' . __DIR__ . '/assets'), [], [], ['php']);
        static::assertCount(3, $files);
    }
}
