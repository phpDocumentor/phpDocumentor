<?php

declare(strict_types=1);

namespace phpDocumentor;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\UriFactory
 */
final class UriFactoryTest extends TestCase
{
    /**
     * @covers ::createUri
     * @dataProvider pathProvider
     */
    public function testCreate(string $input, ?string $scheme, string $path) : void
    {
        $uri = UriFactory::createUri($input);

        self::assertEquals($scheme, $uri->getScheme());
        self::assertEquals($path, $uri->getPath());
    }

    public function pathProvider() : array
    {
        return [
            'absolute windows path without scheme ' => [
                'c:/my/path',
                'file',
                '/c:/my/path',
            ],
            'absolute windows path with scheme ' => [
                'file:///c:/my/path',
                'file',
                '/c:/my/path',
            ],
            'absolute path without scheme ' => [
                '/my/path',
                null,
                '/my/path',
            ],
            'absolute path with scheme ' => [
                'file:///my/path',
                'file',
                '/my/path',
            ],
        ];
    }

    /**
     * @covers ::createUri
     */
    public function testInvalidUriThrowsInvalidArgumentException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        UriFactory::createUri('http:/aaads/@asa:aaa');
    }
}
