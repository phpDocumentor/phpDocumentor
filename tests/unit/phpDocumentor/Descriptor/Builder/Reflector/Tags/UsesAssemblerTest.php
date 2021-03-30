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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler
 * @covers ::<private>
 */
class UsesAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var UsesAssembler $fixture */
    protected $fixture;

    /** @var ApiSetDescriptorBuilder|ObjectProphecy */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->builderMock = $this->prophesize(ApiSetDescriptorBuilder::class);
        $this->fixture = new UsesAssembler();
        $this->fixture->setBuilder($this->builderMock->reveal());
    }

    /**
     * @covers ::create
     * @covers ::buildDescriptor
     */
    public function testCreateUsesDescriptorFromUsesTagWhenReferenceIsRelativeClassnameNotInNamespaceAliasses(): void
    {
        // Arrange
        $name = 'uses';
        $description = 'a uses tag';
        $reference = '\ReferenceClass';
        $usesTagMock = $this->givenAUsesTag($description, $reference);

        // Act
        $descriptor = $this->fixture->create($usesTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, (string) $descriptor->getDescription());
        $this->assertSame($reference, (string) $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    protected function givenAUsesTag($description, $reference): Uses
    {
        return new Uses(
            new Fqsen($reference),
            new DocBlock\Description($description)
        );
    }
}
