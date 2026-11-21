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

namespace phpDocumentor\Descriptor\Tag;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function sys_get_temp_dir;

#[CoversClass(ExampleDescriptor::class)]
final class ExampleDescriptorTest extends TestCase
{
    /** @var ExampleDescriptor $fixture */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = new ExampleDescriptor('name');
    }

    public function testGetName(): void
    {
        self::assertSame('name', $this->fixture->getName());
    }

    public function testItCanHaveAPathToAnExampleFile(): void
    {
        self::assertSame('', $this->fixture->getFilePath());

        $this->fixture->setFilePath(sys_get_temp_dir());

        self::assertSame(sys_get_temp_dir(), $this->fixture->getFilePath());
    }

    public function testItCanDescribeFromWhichLineToShowTheExampleOrNullToShowAll(): void
    {
        self::assertNull($this->fixture->getStartingLine());

        $this->fixture->setStartingLine(100);

        self::assertSame(100, $this->fixture->getStartingLine());
    }

    public function testItCanDescribeHowManyLinesToShowOrNullToShowAll(): void
    {
        self::assertNull($this->fixture->getLineCount());

        $this->fixture->setLineCount(100);

        self::assertSame(100, $this->fixture->getLineCount());
    }

    public function testItCanHaveTheExampleContents(): void
    {
        self::assertSame('', $this->fixture->getExample());

        $this->fixture->setExample('This is an example');

        self::assertSame('This is an example', $this->fixture->getExample());
    }
}
