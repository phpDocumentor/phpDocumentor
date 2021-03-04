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

use PHPUnit\Framework\TestCase;
use function sys_get_temp_dir;

final class ExampleDescriptorTest extends TestCase
{
    /** @var ExampleDescriptor $fixture */
    protected $fixture;

    protected function setUp() : void
    {
        $this->fixture = new ExampleDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::getName
     */
    public function testGetName() : void
    {
        self::assertSame('name', $this->fixture->getName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::getFilePath
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::setFilePath
     */
    public function testItCanHaveAPathToAnExampleFile() : void
    {
        self::assertSame('', $this->fixture->getFilePath());

        $this->fixture->setFilePath(sys_get_temp_dir());

        self::assertSame(sys_get_temp_dir(), $this->fixture->getFilePath());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::getStartingLine
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::setStartingLine
     */
    public function testItCanDescribeFromWhichLineToShowTheExampleOrNullToShowAll() : void
    {
        self::assertNull($this->fixture->getStartingLine());

        $this->fixture->setStartingLine(100);

        self::assertSame(100, $this->fixture->getStartingLine());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::getLineCount
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::setLineCount
     */
    public function testItCanDescribeHowManyLinesToShowOrNullToShowAll() : void
    {
        self::assertNull($this->fixture->getLineCount());

        $this->fixture->setLineCount(100);

        self::assertSame(100, $this->fixture->getLineCount());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::getExample
     * @covers \phpDocumentor\Descriptor\Tag\ExampleDescriptor::setExample
     */
    public function testItCanHaveTheExampleContents() : void
    {
        self::assertSame('', $this->fixture->getExample());

        $this->fixture->setExample('This is an example');

        self::assertSame('This is an example', $this->fixture->getExample());
    }
}
