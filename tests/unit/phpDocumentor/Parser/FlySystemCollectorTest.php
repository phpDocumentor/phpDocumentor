<?php declare(strict_types=1);

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

namespace phpDocumentor\Parser;

use League\Flysystem\MountManager;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Parser\FlySystemCollector;
use phpDocumentor\Parser\SpecificationFactoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FlySystemCollector
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemCollectorTest extends TestCase
{
    /**
     * @covers ::getFiles()
     * @uses \phpDocumentor\Parser\SpecificationFactory
     * @uses \phpDocumentor\Parser\FlySystemFactory
     */
    public function testSingleSourceDir() : void
    {
        $fileCollector = new FlySystemCollector(
            new SpecificationFactory(),
            new FlySystemFactory(new MountManager())
        );

        $files = $fileCollector->getFiles(new Dsn('file://' . __DIR__ . '/assets'), [], [], ['php']);
        static::assertCount(3, $files);
    }
}
