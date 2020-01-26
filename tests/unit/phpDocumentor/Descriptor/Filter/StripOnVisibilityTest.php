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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;

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
    public function testStripsDescriptorIfVisibilityIsNotAllowed() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')
            ->with(Settings::VISIBILITY_PUBLIC)
            ->andReturn(false);

        $descriptor = m::mock(MethodDescriptor::class);
        $descriptor->shouldReceive('getVisibility')->andReturn('public');
        $descriptor->shouldReceive('getTags')->andReturn(new Collection());

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testItNeverStripsDescriptorIfApiIsSet() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')
            ->with(Settings::VISIBILITY_API)->andReturn(true);

        // if API already return true; then we do not expect a call with for the PUBLIC visibility
        $this->builderMock->shouldNotReceive('getProjectDescriptor->isVisibilityAllowed')
            ->with(Settings::VISIBILITY_PUBLIC);

        $descriptor = m::mock(MethodDescriptor::class);
        $descriptor->shouldReceive('getVisibility')->andReturn('public');

        $tagsCollection = new Collection();
        $tagsCollection->set('api', new TagDescriptor('api'));
        $descriptor->shouldReceive('getTags')->andReturn($tagsCollection);

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfVisibilityIsAllowed() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')
            ->with(Settings::VISIBILITY_PUBLIC)
            ->andReturn(true);

        $descriptor = m::mock(MethodDescriptor::class);
        $descriptor->shouldReceive('getVisibility')->andReturn('public');
        $descriptor->shouldReceive('getTags')->andReturn(new Collection());

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfDescriptorNotInstanceOfVisibilityInterface() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock('\phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags')->andReturn(new Collection());

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }
}
