<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Path
 * @covers ::<private>
 */
final class PathTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testItCanContainALocationOnAStorageService(): void
    {
        $path = new Path('/my/Path');

        self::assertSame('/my/Path', (string) $path);
    }

    /**
     * @covers ::equals
     */
    public function testItCanCompareItselfToAnotherPath(): void
    {
        $subject    = new Path('a');
        $similar    = new Path('a');
        $dissimilar = new Path('b');

        self::assertTrue($subject->equals($similar));
        self::assertFalse($subject->equals($dissimilar));
    }

    /**
     * @covers ::isAbsolutePath
     */
    public function testItCanCheckWhetherTheGivenPathIsAnAbsolutePath(): void
    {
        self::assertTrue(Path::isAbsolutePath('\\\\my\\absolute\\path'));
        self::assertTrue(Path::isAbsolutePath('/my/absolute/path'));
        self::assertTrue(Path::isAbsolutePath('c:\\my\\absolute\\path'));
        self::assertTrue(Path::isAbsolutePath('//my/absolute/path'));

        self::assertFalse(Path::isAbsolutePath('path'));
        self::assertFalse(Path::isAbsolutePath('my/absolute/path'));
        self::assertFalse(Path::isAbsolutePath('./my/absolute/path'));
        self::assertFalse(Path::isAbsolutePath('../my/absolute/path'));
    }
}
