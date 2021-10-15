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

class EntryTest extends TestCase
{
    public function test_whether_an_entry_can_be_recorded(): void
    {
        $mtime = time();

        $file = 'example.txt';
        $url = '/docs/example.txt';
        $title = 'Example';
        $titles = [['title1'], ['title2']];
        $tocs = [['dunno?']]; // TODO: What is really going in here?
        $depends = ['other-file.txt'];
        $links = ['another-file']; // TODO: What is really going in here?

        $entry = new Entry($file, $url, $title, $titles, $tocs, $depends, $links, $mtime);

        $this->assertSame($file, $entry->getFile());
        $this->assertSame($url, $entry->getUrl());
        $this->assertSame($title, $entry->getTitle());
        $this->assertSame($titles, $entry->getTitles());
        $this->assertSame($tocs, $entry->getTocs());
        $this->assertSame($depends, $entry->getDepends());
        $this->assertSame($links, $entry->getLinks());
        $this->assertSame($mtime, $entry->getMtime());
    }
}
