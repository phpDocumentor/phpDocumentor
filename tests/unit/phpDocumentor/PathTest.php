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

use phpDocumentor\FileSystem\Path;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\FileSystem\Path */
final class PathTest extends TestCase
{
    public function testItCanContainALocationOnAStorageService(): void
    {
        $path = new Path('/my/Path');

        $this->assertSame('/my/Path', (string) $path);
    }

    public function testItCanCompareItselfToAnotherPath(): void
    {
        $subject    = new Path('a');
        $similar    = new Path('a');
        $dissimilar = new Path('b');

        $this->assertTrue($subject->equals($similar));
        $this->assertFalse($subject->equals($dissimilar));
    }

    public function testItCanCheckWhetherTheGivenPathIsAnAbsolutePath(): void
    {
        $this->assertTrue(Path::isAbsolutePath('\\\\my\\absolute\\path'));
        $this->assertTrue(Path::isAbsolutePath('/my/absolute/path'));
        $this->assertTrue(Path::isAbsolutePath('c:\\my\\absolute\\path'));
        $this->assertTrue(Path::isAbsolutePath('http://my/absolute/path'));
        $this->assertTrue(Path::isAbsolutePath('//my/absolute/path'));

        $this->assertFalse(Path::isAbsolutePath('path'));
        $this->assertFalse(Path::isAbsolutePath('my/absolute/path'));
        $this->assertFalse(Path::isAbsolutePath('./my/absolute/path'));
        $this->assertFalse(Path::isAbsolutePath('../my/absolute/path'));
    }
}
