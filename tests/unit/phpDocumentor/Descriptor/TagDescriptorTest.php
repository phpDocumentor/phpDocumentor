<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use PHPUnit\Framework\TestCase;

class TagDescriptorTest extends TestCase
{
    public const TAG_NAME = 'test';

    private TagDescriptor $fixture;

    /**
     * Instantiates the fixture with its dependencies.
     */
    protected function setUp(): void
    {
        $this->fixture = new TagDescriptor(self::TAG_NAME);
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getName
     * @covers \phpDocumentor\Descriptor\TagDescriptor::setName
     */
    public function testNameIsRegisteredOnInstantiationAndReturned(): void
    {
        self::assertSame(self::TAG_NAME, $this->fixture->getName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getErrors
     */
    public function testIfErrorsAreInitializedToAnEmptyCollectionOnInstantiation(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getErrors());
        self::assertEmpty($this->fixture->getErrors()->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::setErrors
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getErrors
     */
    public function testOverridingErrorsCollectionWithNewCollection(): void
    {
        // Arrange
        $collection = new Collection();

        // Act
        $this->fixture->setErrors($collection);

        // Assert
        self::assertSame($collection, $this->fixture->getErrors());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::setDescription
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getDescription
     */
    public function testSettingAndReturningADescription(): void
    {
        // Arrange
        $description = new DescriptionDescriptor(new Description('Description'), []);
        self::assertEquals(new DescriptionDescriptor(new Description(''), []), $this->fixture->getDescription());

        // Act
        $this->fixture->setDescription($description);

        // Assert
        self::assertSame($description, $this->fixture->getDescription());
    }
}
