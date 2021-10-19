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

namespace phpDocumentor\Guides\Meta;

use PHPUnit\Framework\TestCase;

use function time;

/**
 * @coversDefaultClass \phpDocumentor\Guides\Meta\Entry
 * @covers ::<private>
 */
final class EntryTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getFile
     * @covers ::getUrl
     * @covers ::getTitle
     * @covers ::getTitles
     * @covers ::getTocs
     * @covers ::getDepends
     * @covers ::getLinks
     * @covers ::getMtime
     */
    public function testWhetherAnEntryCanBeRecorded(): void
    {
        $mtime = time();

        $file = 'example.txt';
        $url = '/docs/example.txt';
        $title = 'Example';
        $titles = [['title1'], ['title2']];
        $tocs = [['dunno?']];
        $depends = ['other-file.txt'];
        $links = ['another-file'];

        $entry = new Entry($file, $url, $title, $titles, $tocs, $depends, $links, $mtime);

        self::assertSame($file, $entry->getFile());
        self::assertSame($url, $entry->getUrl());
        self::assertSame($title, $entry->getTitle());
        self::assertSame($titles, $entry->getTitles());
        self::assertSame($tocs, $entry->getTocs());
        self::assertSame($depends, $entry->getDepends());
        self::assertSame($links, $entry->getLinks());
        self::assertSame($mtime, $entry->getMtime());
    }

    /**
     * @covers ::getParent
     * @covers ::setParent
     */
    public function testSettingAParentForAMetaEntry(): void
    {
        $entry = new Entry(
            'example.txt',
            '/docs/example.txt',
            'Example',
            [['title1'], ['title2']],
            [['dunno?']],
            ['other-file.txt'],
            ['another-file'],
            time()
        );

        $entry->setParent('parent');

        self::assertSame('parent', $entry->getParent());
    }
}
