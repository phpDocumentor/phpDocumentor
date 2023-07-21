<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use Mockery as m;

trait TraitUsageTests
{
    /** @covers ::getInheritedMethods */
    public function testRetrievingInheritedMethodsReturnsTraitMethods(): void
    {
        // Arrange
        $expected            = ['methods'];
        $traitDescriptorMock = m::mock(TraitDescriptor::class);
        $traitDescriptorMock->shouldReceive('getMethods')->andReturn(new Collection(['methods']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock]));

        // Act
        $result = $this->fixture->getInheritedMethods();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::getInheritedMethods
     * @ticket https://github.com/phpDocumentor/phpDocumentor2/issues/1307
     */
    public function testRetrievingInheritedMethodsDoesNotCrashWhenUsedTraitIsNotInProject(): void
    {
        // Arrange
        $expected = [];
        // unknown traits are not converted to TraitDescriptors but kept as strings
        $this->fixture->setUsedTraits(new Collection(['unknownTrait']));

        // Act
        $result = $this->fixture->getInheritedMethods();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }
}
