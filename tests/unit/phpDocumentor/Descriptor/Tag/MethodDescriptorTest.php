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
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the MethodDescriptor class.
 */
class MethodDescriptorTest extends TestCase
{
    public const EXAMPLE_NAME = 'methodname';

    /** @var MethodDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new MethodDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::setMethodName
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::getMethodName
     */
    public function testSetAndGetMethodName() : void
    {
        self::assertEmpty($this->fixture->getMethodName());

        $this->fixture->setMethodName(self::EXAMPLE_NAME);
        $result = $this->fixture->getMethodName();

        self::assertSame(self::EXAMPLE_NAME, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::setArguments()
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::getArguments()
     */
    public function testSetAndGetArguments() : void
    {
        $expected = new Collection(['a' => 'b']);
        self::assertInstanceOf(Collection::class, $this->fixture->getArguments());

        $this->fixture->setArguments($expected);
        $result = $this->fixture->getArguments();

        self::assertSame($expected, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::setResponse
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::getResponse
     */
    public function testSetAndGetResponse() : void
    {
        $expected = new ReturnDescriptor('a');
        self::assertNull($this->fixture->getResponse());

        $this->fixture->setResponse($expected);
        $result = $this->fixture->getResponse();

        self::assertSame($expected, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::setStatic
     * @covers \phpDocumentor\Descriptor\Tag\MethodDescriptor::isStatic
     */
    public function testRecordIfAMethodIsStatic() : void
    {
        self::assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        self::assertTrue($this->fixture->isStatic());
    }
}
