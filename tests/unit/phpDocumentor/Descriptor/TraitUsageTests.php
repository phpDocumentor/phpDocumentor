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

namespace phpDocumentor\Descriptor;

use PHPUnit\Framework\Attributes\Ticket;
use Prophecy\PhpUnit\ProphecyTrait;

trait TraitUsageTests
{
    use ProphecyTrait;

    public function testRetrievingInheritedMethodsReturnsTraitMethods(): void
    {
        // Arrange
        $expected            = ['methods'];
        $traitDescriptorMock = $this->prophesize(TraitDescriptor::class);
        $traitDescriptorMock->getMethods()->willReturn(new Collection(['methods']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock->reveal()]));

        // Act
        $result = $this->fixture->getInheritedMethods();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }

    #[Ticket('https://github.com/phpDocumentor/phpDocumentor2/issues/1307')]
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
