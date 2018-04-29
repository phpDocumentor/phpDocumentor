<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Type;

class CollectionDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var CollectionDescriptor */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp()
    {
        $this->fixture = new CollectionDescriptor('array');
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::getName
     */
    public function testRetrieveNameForBaseTypeWithTypeString()
    {
        $this->assertSame('array', $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::getName
     */
    public function testRetrieveNameForBaseTypeWithTypeDescriptor()
    {
        $fixture = new CollectionDescriptor(new UnknownTypeDescriptor('array'));

        $this->assertSame('array', $fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::getBaseType
     */
    public function testRetrieveBaseTypeWithTypeStringReturnsNull()
    {
        $this->assertNull($this->fixture->getBaseType());
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::getBaseType
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::setBaseType
     */
    public function testSetAndRetrieveBaseTypeWithTypeDescriptor()
    {
        $expected = new UnknownTypeDescriptor('array');
        $this->fixture->setBaseType($expected);

        $this->assertSame($expected, $this->fixture->getBaseType());
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::setTypes
     */
    public function testSetAndRetrieveTypes()
    {
        $expected = new UnknownTypeDescriptor('array');
        $this->fixture->setTypes([$expected]);

        $this->assertSame([$expected], $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::getKeyTypes
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::setKeyTypes
     */
    public function testSetAndRetrieveKeyTypes()
    {
        $expected = new UnknownTypeDescriptor('string');
        $this->fixture->setKeyTypes([$expected]);

        $this->assertSame([$expected], $this->fixture->getKeyTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::__toString
     */
    public function testRetrieveCollectionNotationFromObject()
    {
        $this->fixture->setKeyTypes([new StringDescriptor()]);
        $this->fixture->setTypes([new FloatDescriptor(), new IntegerDescriptor()]);

        $this->assertSame('array<string,float|integer>', (string) $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Type\CollectionDescriptor::__toString
     */
    public function testRetrieveCollectionNotationFromObjectWithoutKeys()
    {
        $this->fixture->setTypes([new FloatDescriptor(), new IntegerDescriptor()]);

        $this->assertSame('array<float|integer>', (string) $this->fixture);
    }
}
