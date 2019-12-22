<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use League\Flysystem\MountManager;
use phpDocumentor\Dsn;
use PHPUnit\Framework\TestCase;
use const DIRECTORY_SEPARATOR;
use const PHP_OS;
use function stripos;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FlySystemCollector
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemCollectorTest extends TestCase
{
    /**
     * @uses \phpDocumentor\Parser\SpecificationFactory
     * @uses \phpDocumentor\Parser\FlySystemFactory
     *
     * @covers ::getFiles()
     */
    public function testSingleSourceDir() : void
    {
        $fileCollector = new FlySystemCollector(
            new SpecificationFactory(),
            new FlySystemFactory(new MountManager())
        );

        $files = $fileCollector->getFiles(
            new Dsn($this->scheme() . __DIR__ . DIRECTORY_SEPARATOR . 'assets'),
            [],
            [],
            ['php']
        );
        static::assertCount(3, $files);
    }

    private function scheme() : string
    {
        $scheme = 'file://';

        // When using the file:// scheme on windows in combination with an absolute path; you need an extra /
        // See https://blogs.msdn.microsoft.com/ie/2006/12/06/file-uris-in-windows/
        if (stripos(PHP_OS, 'WIN') === 0) {
            $scheme .= '/';
        }
        return $scheme;
    }
}
