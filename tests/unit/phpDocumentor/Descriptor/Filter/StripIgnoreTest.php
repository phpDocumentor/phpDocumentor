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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Tests the functionality for the StripIgnore class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripIgnore
 */
final class StripIgnoreTest extends MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    private $builderMock;

    /** @var StripIgnore $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new StripIgnore($this->builderMock);
    }

    /**
     * @covers ::__invoke
     */
    public function testStripsIgnoreTagFromDescription() : void
    {
        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getTags->fetch')->with('ignore')->andReturn(true);

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoIgnoreTag() : void
    {
        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getTags->fetch')->with('ignore')->andReturn(false);

        $this->assertEquals($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testNullIsReturnedIfThereIsNoDescriptor() : void
    {
        $this->assertNull($this->fixture->__invoke(null));
    }
}
