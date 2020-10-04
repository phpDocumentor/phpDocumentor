<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Tag\PropertyDescriptor as TagPropertyDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Types\String_;

trait MagicPropertyContainerTests
{
    /**
     * @covers ::getMagicProperties
     */
    public function testGetMagicPropertiesUsingPropertyTags() : void
    {
        $variableName = 'variableName';
        $description = new DescriptionDescriptor(new Description('description'), []);
        $propertyTag = new TagPropertyDescriptor('property', $description);
        $propertyTag->setType(new String_());
        $propertyTag->setVariableName($variableName);

        $this->fixture->getTags()->fetch('property', new Collection())->add($propertyTag);

        $magicProperties = $this->fixture->getMagicProperties();

        $expected = new PropertyDescriptor();
        $expected->setType(new String_());
        $expected->setName($variableName);
        $expected->setDescription($description);
        $expected->setParent($this->fixture);

        self::assertEquals(
            new Collection([$expected]),
            $magicProperties
        );
    }

    /**
     * @covers ::getMagicProperties
     */
    public function testGetMagicPropertyWithoutName() : void
    {
        $description = new DescriptionDescriptor(new Description('description'), []);

        self::assertEquals(0, $this->fixture->getMagicProperties()->count());

        $propertyTag = new TagPropertyDescriptor('property', $description);
        $propertyTag->setType(new String_());

        $this->fixture->getTags()->fetch('property', new Collection())->add($propertyTag);

        $magicProperties = $this->fixture->getMagicProperties();

        self::assertCount(1, $magicProperties);
        self::assertSame($description, $magicProperties->get(0)->getDescription());
        self::assertEquals(new String_(), $magicProperties->get(0)->getType());
        self::assertCount(1, $magicProperties->get(0)->getErrors());
    }

    /**
     * @covers ::getMagicProperties
     */
    public function testMagicPropertiesReturnsEmptyCollectionWhenNoTags() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMagicProperties());

        $collection = $this->fixture->getMagicProperties();

        $this->assertEquals(0, $collection->count());
    }
}
