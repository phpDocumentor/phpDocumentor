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

use _HumbugBox7eb78fbcc73e\phpDocumentor\Reflection\DocBlock\Description;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Description as DocBlockDescription;

/**
 * Tests the functionality for the StripInternal class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripInternal
 */
final class StripInternalTest extends MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    private $builderMock;

    /** @var StripInternal $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new StripInternal($this->builderMock);
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
                $otherTag
            ]
        );

        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);
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

        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);
        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($description);
        $this->assertSame($tags, $this->fixture->__invoke($descriptor)->getDescription()->getTags());
    }

    /**
     * @covers ::__invoke
     */
    public function testRemovesDescriptorIfTaggedAsInternal() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->fetch')->with('internal')->andReturn(true);

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfTaggedAsInternalAndParsePrivateIsTrue() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(true);

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoInternalTag() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(false);

        $this->assertEquals($descriptor, $this->fixture->__invoke($descriptor));
    }
}
