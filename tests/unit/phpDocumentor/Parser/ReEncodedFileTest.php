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

namespace phpDocumentor\Parser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\String\UnicodeString;

use function md5;

/** @coversDefaultClass \phpDocumentor\Parser\ReEncodedFile */
final class ReEncodedFileTest extends TestCase
{
    public function testReEncodedFileExposesPath(): void
    {
        $path = '/tmp/fileToBeParsed';
        $file = new ReEncodedFile($path, new UnicodeString('Contents'));

        $this->assertSame($path, $file->path());
    }

    public function testReEncodedFileExposesContents(): void
    {
        $contents = 'Contents';
        $file = new ReEncodedFile('/tmp/fileToBeParsed', new UnicodeString($contents));

        $this->assertSame($contents, $file->getContents());
    }

    public function testReEncodedFileExposesHashOfContents(): void
    {
        $contents = 'Contents';
        $file = new ReEncodedFile('/tmp/fileToBeParsed', new UnicodeString($contents));

        $this->assertSame(md5($contents), $file->md5());
    }
}
