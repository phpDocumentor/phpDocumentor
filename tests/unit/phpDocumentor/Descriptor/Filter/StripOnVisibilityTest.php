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
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Tests the functionality for the StripOnVisibility class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripOnVisibility
 */
final class StripOnVisibilityTest extends MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    private $builderMock;

    /** @var StripOnVisibility $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new StripOnVisibility($this->builderMock);
    }

    /**
     * @covers ::__invoke
     */
    public function testStripsTagFromDescriptionIfVisibilityIsNotAllowed() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock(MethodDescriptor::class);
        $descriptor->shouldReceive('getVisibility')->andReturn('public');

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfVisibilityIsAllowed() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock(MethodDescriptor::class);
        $descriptor->shouldReceive('getVisibility')->andReturn('public');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfDescriptorNotInstanceOfVisibilityInterface() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock('\phpDocumentor\Descriptor\DescriptorAbstract');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }
}
