<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Parser;

use phpDocumentor\FileSystem\Finder\Exclude;
use phpDocumentor\FileSystem\Finder\SpecificationFactory;
use phpDocumentor\FileSystem\FlySystemAdapter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function stripos;

use const DIRECTORY_SEPARATOR;
use const PHP_OS;

/** @coversDefaultClass \phpDocumentor\Parser\FlySystemCollector */
final class FlySystemCollectorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @uses \phpDocumentor\FileSystem\Finder\SpecificationFactory
     * @uses \phpDocumentor\FileSystem\FlySystemFactory
     */
    public function testSingleSourceDir(): void
    {
        $fileCollector = new FlySystemCollector(
            new SpecificationFactory(),
        );

        $files = $fileCollector->getFiles(
            FlySystemAdapter::createForPath(__DIR__ . DIRECTORY_SEPARATOR . 'assets'),
            [],
            new Exclude([]),
            ['php'],
        );
        static::assertCount(3, $files);
    }

    private function scheme(): string
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
