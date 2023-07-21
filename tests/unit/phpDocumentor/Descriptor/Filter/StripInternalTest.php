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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Description as DocBlockDescription;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the StripInternal class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripInternal
 */
final class StripInternalTest extends TestCase
{
    use ProphecyTrait;

    private StripInternal $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new StripInternal();
    }

    /**
     * @uses \phpDocumentor\Descriptor\ClassDescriptor
     *
     * @covers ::__invoke
     */
    public function testStripsInternalTagFromDescription(): void
    {
        $otherTag = new TagDescriptor('other');
        $description = new DescriptionDescriptor(
            new DocBlockDescription('irelavant'),
            [
                new TagDescriptor('internal'),
                $otherTag,
            ],
        );

        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['public'];

        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($description);
        $this->assertSame(
            [null, $otherTag],
            $this->fixture->__invoke(
                new FilterPayload($descriptor, $apiSpec),
            )->getFilterable()->getDescription()->getTags(),
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\ClassDescriptor
     *
     * @covers ::__invoke
     */
    public function testKeepsInternalTagsInDescription(): void
    {
        $tags = [
            new TagDescriptor('internal'),
            new TagDescriptor('other'),
        ];

        $description = new DescriptionDescriptor(
            new DocBlockDescription('irelavant'),
            $tags,
        );

        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['internal'];

        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($description);
        self::assertSame(
            $tags,
            $this->fixture->__invoke(
                new FilterPayload($descriptor, $apiSpec),
            )->getFilterable()->getDescription()->getTags(),
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testRemovesDescriptorIfTaggedAsInternal(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['public'];

        $collection = $this->prophesize(Collection::class);
        $collection->fetch('internal')->shouldBeCalled()->willReturn(true);

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getDescription()->shouldBeCalled()->willReturn(DescriptionDescriptor::createEmpty());
        $descriptor->getTags()->shouldBeCalled()->willReturn($collection->reveal());

        self::assertNull(
            $this->fixture->__invoke(new FilterPayload($descriptor->reveal(), $apiSpec))->getFilterable(),
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfTaggedAsInternalAndParsePrivateIsTrue(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['internal'];

        $descriptor = $this->prophesize(DescriptorAbstract::class);

        self::assertSame(
            $descriptor->reveal(),
            $this->fixture->__invoke(new FilterPayload($descriptor->reveal(), $apiSpec))->getFilterable(),
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoInternalTag(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getDescription()->willReturn(DescriptionDescriptor::createEmpty());
        $descriptor->getTags()->willReturn(new Collection());

        self::assertSame(
            $descriptor->reveal(),
            $this->fixture->__invoke(new FilterPayload($descriptor->reveal(), $apiSpec))->getFilterable(),
        );
    }
}
