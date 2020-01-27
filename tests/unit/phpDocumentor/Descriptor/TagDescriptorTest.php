<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class TagDescriptorTest extends MockeryTestCase
{
    public const TAG_NAME = 'test';

    /** @var TagDescriptor */
    private $fixture;

    /**
     * Instantiates the fixture with its dependencies.
     */
    protected function setUp() : void
    {
        $this->fixture = new TagDescriptor(self::TAG_NAME);
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getName
     * @covers \phpDocumentor\Descriptor\TagDescriptor::setName
     */
    public function testNameIsRegisteredOnInstantiationAndReturned() : void
    {
        $this->assertSame(self::TAG_NAME, $this->fixture->getName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getErrors
     */
    public function testIfErrorsAreInitializedToAnEmptyCollectionOnInstantiation() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getErrors());
        $this->assertEmpty($this->fixture->getErrors()->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::setErrors
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getErrors
     */
    public function testOverridingErrorsCollectionWithNewCollection() : void
    {
        // Arrange
        $collection = new Collection();

        // Act
        $this->fixture->setErrors($collection);

        // Assert
        $this->assertSame($collection, $this->fixture->getErrors());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TagDescriptor::setDescription
     * @covers \phpDocumentor\Descriptor\TagDescriptor::getDescription
     */
    public function testSettingAndReturningADescription() : void
    {
        // Arrange
        $description = 'Description';
        $this->assertSame('', $this->fixture->getDescription());

        // Act
        $this->fixture->setDescription($description);

        // Assert
        $this->assertSame($description, $this->fixture->getDescription());
    }
}
