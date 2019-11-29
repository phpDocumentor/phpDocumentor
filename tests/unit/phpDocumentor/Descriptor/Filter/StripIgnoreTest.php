<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Filter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Tests the functionality for the StripIgnore class.
 */
class StripIgnoreTest extends MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    protected $builderMock;

    /** @var StripIgnore $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture     = new StripIgnore($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__invoke
     */
    public function testStripsIgnoreTagFromDescription() : void
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('ignore')->andReturn(true);

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoIgnoreTag() : void
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('ignore')->andReturn(false);

        $this->assertEquals($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__invoke
     */
    public function testNullIsReturnedIfThereIsNoDescriptor() : void
    {
        $this->assertNull($this->fixture->__invoke(null));
    }
}
