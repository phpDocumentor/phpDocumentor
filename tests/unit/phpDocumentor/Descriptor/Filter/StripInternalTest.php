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

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Description as DocBlockDescription;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Tests the functionality for the StripInternal class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripInternal
 */
final class StripInternalTest extends TestCase
{
    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private $builderMock;

    /** @var ProjectDescriptor|ObjectProphecy */
    private $projectDescriptor;

    /** @var StripInternal $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->projectDescriptor = $this->prophesize(ProjectDescriptor::class);
        $this->builderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->builderMock->getProjectDescriptor()->shouldBeCalled()->willReturn($this->projectDescriptor->reveal());
        $this->fixture = new StripInternal($this->builderMock->reveal());
    }

    /**
     * @uses \phpDocumentor\Descriptor\ClassDescriptor
     *
     * @covers ::__invoke
     */
    public function testStripsInternalTagFromDescription() : void
    {
        $otherTag = new TagDescriptor('other');
        $description = new DescriptionDescriptor(
            new DocBlockDescription('irelavant'),
            [
                new TagDescriptor('internal'),
                $otherTag,
            ]
        );

        $this->projectDescriptor->isVisibilityAllowed(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);

        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($description);
        $this->assertSame([null, $otherTag], $this->fixture->__invoke($descriptor)->getDescription()->getTags());
    }

    /**
     * @uses \phpDocumentor\Descriptor\ClassDescriptor
     *
     * @covers ::__invoke
     */
    public function testKeepsInternalTagsInDescription() : void
    {
        $tags = [
            new TagDescriptor('internal'),
            new TagDescriptor('other'),
        ];

        $description = new DescriptionDescriptor(
            new DocBlockDescription('irelavant'),
            $tags
        );

        $this->projectDescriptor->isVisibilityAllowed(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);

        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($description);
        $this->assertSame($tags, $this->fixture->__invoke($descriptor)->getDescription()->getTags());
    }

    /**
     * @covers ::__invoke
     */
    public function testRemovesDescriptorIfTaggedAsInternal() : void
    {
        $this->projectDescriptor->isVisibilityAllowed(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);

        $collection = $this->prophesize(Collection::class);
        $collection->fetch('internal')->shouldBeCalled()->willReturn(true);

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getDescription()->shouldBeCalled()->willReturn();
        $descriptor->getTags()->shouldBeCalled()->willReturn($collection->reveal());

        $this->assertNull($this->fixture->__invoke($descriptor->reveal()));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfTaggedAsInternalAndParsePrivateIsTrue() : void
    {
        $this->projectDescriptor->isVisibilityAllowed(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);

        $descriptor = $this->prophesize(DescriptorAbstract::class);

        $this->assertSame($descriptor->reveal(), $this->fixture->__invoke($descriptor->reveal()));
    }

    /**
     * @covers ::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoInternalTag() : void
    {
        $this->projectDescriptor->isVisibilityAllowed(Argument::any())
            ->shouldBeCalled()
            ->willReturn(true);

        $descriptor = $this->prophesize(DescriptorAbstract::class);

        $this->assertEquals($descriptor->reveal(), $this->fixture->__invoke($descriptor->reveal()));
    }
}
