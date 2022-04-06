<?php

declare(strict_types=1);

namespace unit;

use phpDocumentor\Guides\UrlGenerator;
use PHPUnit\Framework\TestCase;

final class UrlGeneratorTest extends TestCase
{
    /** @dataProvider cannicalUrlProvider */
    public function testCannicalUrl(string $basePath, string $url, string $result): void
    {
        $urlGenerator = new UrlGenerator();
        self::assertSame($result, $urlGenerator->canonicalUrl($basePath, $url));
    }

    public function cannicalUrlProvider(): array
    {
        return [
            [
                'basePath' => 'dir',
                'url' => 'file',
                'result' => 'dir/file',
            ],
            [
                'basePath' => 'dir',
                'url' => '../file',
                'result' => 'file',
            ],
            [
                'basePath' => 'dir/subdir',
                'url' => '../file',
                'result' => 'dir/file',
            ],
            [
                'basePath' => 'dir/subdir',
                'url' => '../../file',
                'result' => 'file',
            ],
            [
                'basePath' => 'dir/subdir',
                'url' => '.././file',
                'result' => 'dir/file',
            ],
            [
                'basePath' => 'dir/subdir',
                'url' => './file',
                'result' => 'dir/subdir/file',
            ],
        ];
    }

    /** @dataProvider abstractUrlProvider */
    public function testAbsoluteUrl(string $basePath, string $url, string $result): void
    {
        $urlGenerator = new UrlGenerator();
        self::assertSame($result, $urlGenerator->absoluteUrl($basePath, $url));
    }

    public function abstractUrlProvider(): array
    {
        return [
            [
                'basePath' => '/',
                'url' => 'file',
                'result' => '/file',
            ],
            [
                'basePath' => '/foo',
                'url' => '/file',
                'result' => '/file',
            ],
            [
                'basePath' => '/dir',
                'url' => 'file',
                'result' => '/dir/file',
            ],
            [
                'basePath' => '/dir/',
                'url' => 'file',
                'result' => '/dir/file',
            ],
        ];
    }
}
