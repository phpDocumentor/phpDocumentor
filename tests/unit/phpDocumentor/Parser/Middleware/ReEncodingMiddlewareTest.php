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

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\ReEncodedFile;
use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File as PhpFile;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\String\Exception\InvalidArgumentException;
use Symfony\Component\String\UnicodeString;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Middleware\ReEncodingMiddleware
 * @covers ::<private>
 */
final class ReEncodingMiddlewareTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /**
     * @covers ::withEncoding
     * @covers ::execute
     */
    public function testItReencodesFileContentsUsingTheGivenEncoding(): void
    {
        $contents = new UnicodeString('äNDERUNGEN');
        $file = $this->createFileWithIso8859EncodedContents($contents);

        $middleware = new ReEncodingMiddleware();
        $middleware->withEncoding('iso-8859-1');
        $result = $middleware->execute(
            new CreateCommand($this->faker()->phpParserContext(), $file, new ProjectFactoryStrategies([])),
            function (CreateCommand $command) use ($contents): PhpFile {
                $reEncodedFile = $command->getFile();
                $this->assertInstanceOf(ReEncodedFile::class, $reEncodedFile);

                // only when the file has been re-encoded to UTF-8 is the output the same as $contents
                // if there was no re-encoding, then this test would fail.
                $this->assertSame($contents->toString(), $reEncodedFile->getContents());

                return new PhpFile($reEncodedFile->md5(), $reEncodedFile->path());
            }
        );

        $this->assertInstanceOf(PhpFile::class, $result);
    }

    /**
     * @covers ::execute
     */
    public function testItFailsToReEncodeFileIfTheGivenEncodingIsWrong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid "utf-8" string.');

        $contents = new UnicodeString('äNDERUNGEN');
        $file = $this->createFileWithIso8859EncodedContents($contents);

        $middleware = new ReEncodingMiddleware();
        $middleware->withEncoding('utf-8');
        $middleware->execute(
            new CreateCommand(
                $this->faker()->phpParserContext(),
                $file,
                new ProjectFactoryStrategies([])
            ),
            static function (): void {
                // not important; never called due to exception
            }
        );
    }

    private function createFileWithIso8859EncodedContents(UnicodeString $contents): File
    {
        $file = $this->prophesize(File::class);
        $file->path()->willReturn('/tmp/fakeFile.php');
        $file->getContents()->willReturn($contents->toByteString('iso-8859-1')->toString());

        return $file->reveal();
    }
}
