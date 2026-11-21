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

use phpDocumentor\Descriptor\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the MethodDescriptor class.
 */
#[CoversClass(MethodDescriptor::class)]
class MethodDescriptorTest extends TestCase
{
    final public const EXAMPLE_NAME = 'methodname';

    /** @var MethodDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new MethodDescriptor('name');
    }

    public function testSetAndGetMethodName(): void
    {
        self::assertEmpty($this->fixture->getMethodName());

        $this->fixture->setMethodName(self::EXAMPLE_NAME);
        $result = $this->fixture->getMethodName();

        self::assertSame(self::EXAMPLE_NAME, $result);
    }

    public function testSetAndGetArguments(): void
    {
        $expected = new Collection(['a' => 'b']);
        self::assertInstanceOf(Collection::class, $this->fixture->getArguments());

        $this->fixture->setArguments($expected);
        $result = $this->fixture->getArguments();

        self::assertSame($expected, $result);
    }

    public function testSetAndGetResponse(): void
    {
        $expected = new ReturnDescriptor('a');
        self::assertNull($this->fixture->getResponse());

        $this->fixture->setResponse($expected);
        $result = $this->fixture->getResponse();

        self::assertSame($expected, $result);
    }

    public function testRecordIfAMethodIsStatic(): void
    {
        self::assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        self::assertTrue($this->fixture->isStatic());
    }
}
