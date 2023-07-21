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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the StripOnVisibility class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripOnVisibility
 */
final class StripOnVisibilityTest extends TestCase
{
    use ProphecyTrait;

    private StripOnVisibility $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new StripOnVisibility();
    }

    /** @covers ::__invoke */
    public function testStripsDescriptorIfVisibilityIsNotAllowed(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['api'];

        $descriptor = $this->prophesize(MethodDescriptor::class);
        $descriptor->getVisibility()->shouldBeCalled()->willReturn('public');
        $descriptor->getTags()->shouldBeCalled()->willReturn(new Collection());

        self::assertNull(
            $this->fixture->__invoke(
                new FilterPayload($descriptor->reveal(), $apiSpec),
            )->getFilterable(),
        );
    }

    /** @covers ::__invoke */
    public function testItNeverStripsDescriptorIfApiIsSet(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['api'];

        $descriptor = $this->prophesize(MethodDescriptor::class);

        $tagsCollection = new Collection();
        $tagsCollection->set('api', new TagDescriptor('api'));
        $descriptor->getTags()->shouldBeCalled()->willReturn($tagsCollection);

        self::assertSame(
            $descriptor->reveal(),
            $this->fixture->__invoke(
                new FilterPayload($descriptor->reveal(), $apiSpec),
            )->getFilterable(),
        );
    }

    /** @covers ::__invoke */
    public function testKeepsDescriptorIfVisibilityIsAllowed(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['public'];

        $descriptor = $this->prophesize(MethodDescriptor::class);
        $descriptor->getVisibility()->shouldBeCalled()->willReturn('public');
        $descriptor->getTags()->shouldBeCalled()->willReturn(new Collection());

        self::assertSame(
            $descriptor->reveal(),
            $this->fixture->__invoke(
                new FilterPayload($descriptor->reveal(), $apiSpec),
            )->getFilterable(),
        );
    }

    /** @covers ::__invoke */
    public function testKeepsDescriptorIfDescriptorNotInstanceOfVisibilityInterface(): void
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = ['public'];

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getTags()->shouldBeCalled()->willReturn(new Collection());

        self::assertSame(
            $descriptor->reveal(),
            $this->fixture->__invoke(
                new FilterPayload($descriptor->reveal(), $apiSpec),
            )->getFilterable(),
        );
    }
}
