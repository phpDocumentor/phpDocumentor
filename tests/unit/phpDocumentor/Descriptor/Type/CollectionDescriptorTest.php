<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Type;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Type\CollectionDescriptor
 */
final class CollectionDescriptorTest extends MockeryTestCase
{
    /** @var CollectionDescriptor */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp() : void
    {
        $this->fixture = new CollectionDescriptor();
    }

    /**
     * @covers ::getName()
     */
    public function testRetrieveNameForBaseTypeWithTypeString() : void
    {
        $this->assertSame('array', $this->fixture->getName());
    }

    /**
     * @covers ::getName()
     */
    public function testRetrieveNameForBaseTypeWithTypeDescriptor() : void
    {
        $fixture = new CollectionDescriptor(new UnknownTypeDescriptor('array'));

        $this->assertSame('array', $fixture->getName());
    }

    /**
     * @covers ::getBaseType()
     */
    public function testRetrieveBaseTypeWithTypeStringReturnsNull() : void
    {
        $fixture = new CollectionDescriptor();

        $this->assertNull($fixture->getBaseType());
    }

    /**
     * @covers ::getBaseType()
     * @covers ::setBaseType()
     */
    public function testSetAndRetrieveBaseTypeWithTypeDescriptor() : void
    {
        $expected = new UnknownTypeDescriptor('array');
        $this->fixture->setBaseType($expected);

        $this->assertSame($expected, $this->fixture->getBaseType());
    }

    /**
     * @covers ::getTypes()
     * @covers ::setTypes()
     */
    public function testSetAndRetrieveTypes() : void
    {
        $expected = new UnknownTypeDescriptor('array');
        $this->fixture->setTypes([$expected]);

        $this->assertSame([$expected], $this->fixture->getTypes());
    }

    /**
     * @covers ::getKeyTypes()
     * @covers ::setKeyTypes()
     */
    public function testSetAndRetrieveKeyTypes() : void
    {
        $expected = new UnknownTypeDescriptor('string');
        $this->fixture->setKeyTypes([$expected]);

        $this->assertSame([$expected], $this->fixture->getKeyTypes());
    }

    /**
     * @covers ::__toString()
     */
    public function testRetrieveCollectionNotationFromObject() : void
    {
        $this->fixture->setKeyTypes([new StringDescriptor()]);
        $this->fixture->setTypes([new FloatDescriptor(), new IntegerDescriptor()]);

        $this->assertSame('array<string,float|integer>', (string) $this->fixture);
    }

    /**
     * @covers ::__toString()
     */
    public function testRetrieveCollectionNotationFromObjectWithoutKeys() : void
    {
        $this->fixture->setTypes([new FloatDescriptor(), new IntegerDescriptor()]);

        $this->assertSame('array<float|integer>', (string) $this->fixture);
    }
}
