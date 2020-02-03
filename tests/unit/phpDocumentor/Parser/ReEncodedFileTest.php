<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\String\UnicodeString;
use function md5;

/**
 * @coversDefaultClass \phpDocumentor\Parser\ReEncodedFile
 * @covers ::__construct
 * @covers ::<private>
 */
final class ReEncodedFileTest extends TestCase
{
    /**
     * @covers ::path
     */
    public function testReEncodedFileExposesPath() : void
    {
        $path = '/tmp/fileToBeParsed';
        $file = new ReEncodedFile($path, new UnicodeString('Contents'));

        $this->assertSame($path, $file->path());
    }

    /**
     * @covers ::getContents
     */
    public function testReEncodedFileExposesContents() : void
    {
        $contents = 'Contents';
        $file = new ReEncodedFile('/tmp/fileToBeParsed', new UnicodeString($contents));

        $this->assertSame($contents, $file->getContents());
    }

    /**
     * @covers ::md5
     */
    public function testReEncodedFileExposesHashOfContents() : void
    {
        $contents = 'Contents';
        $file = new ReEncodedFile('/tmp/fileToBeParsed', new UnicodeString($contents));

        $this->assertSame(md5($contents), $file->md5());
    }
}
