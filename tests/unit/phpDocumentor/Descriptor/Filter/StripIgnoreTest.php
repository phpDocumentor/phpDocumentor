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
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the StripIgnore class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripIgnore
 */
final class StripIgnoreTest extends TestCase
{
    use ProphecyTrait;

    private StripIgnore $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new StripIgnore();
    }

    /** @covers ::__invoke */
    public function testStripsIgnoreTagFromDescription(): void
    {
        $collection = $this->prophesize(Collection::class);
        $collection->fetch('ignore')->shouldBeCalled()->willReturn(true);

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getTags()->shouldBeCalled()->willReturn($collection->reveal());

        $this->assertNull(
            $this->fixture->__invoke(
                new FilterPayload($descriptor->reveal(), ApiSpecification::createDefault()),
            )->getFilterable(),
        );
    }

    /** @covers ::__invoke */
    public function testDescriptorIsUnmodifiedIfThereIsNoIgnoreTag(): void
    {
        $collection = $this->prophesize(Collection::class);
        $collection->fetch('ignore')->shouldBeCalled()->willReturn(false);

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getTags()->shouldBeCalled()->willReturn($collection->reveal());

        $this->assertEquals(
            $descriptor->reveal(),
            $this->fixture->__invoke(
                new FilterPayload($descriptor->reveal(), ApiSpecification::createDefault()),
            )->getFilterable(),
        );
    }
}
