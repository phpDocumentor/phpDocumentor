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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Tests the functionality for the StripOnVisibility class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripOnVisibility
 */
final class StripOnVisibilityTest extends TestCase
{
    use ProphecyTrait;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private $builderMock;

    /** @var ProjectDescriptor|ObjectProphecy */
    private $projectDescriptor;

    /** @var StripOnVisibility $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->projectDescriptor = $this->prophesize(ProjectDescriptor::class);
        $this->builderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->builderMock->getProjectDescriptor()->shouldBeCalled()->willReturn($this->projectDescriptor->reveal());
        $this->fixture = new StripOnVisibility($this->builderMock->reveal());
    }

    /**
     * @covers ::__invoke
     */
    public function testStripsDescriptorIfVisibilityIsNotAllowed() : void
    {
        $this->projectDescriptor->isVisibilityAllowed(Argument::exact(Settings::VISIBILITY_PUBLIC))
            ->shouldBeCalled()
            ->willReturn(false);

        $descriptor = $this->prophesize(MethodDescriptor::class);
        $descriptor->getVisibility()->shouldBeCalled()->willReturn('public');
        $descriptor->getTags()->shouldBeCalled()->willReturn(new Collection());

        $this->assertNull($this->fixture->__invoke($descriptor->reveal()));
    }

    /**
     * @covers ::__invoke
     */
    public function testItNeverStripsDescriptorIfApiIsSet() : void
    {
        $this->projectDescriptor->isVisibilityAllowed(Argument::exact(Settings::VISIBILITY_API))
            ->shouldBeCalled()
            ->willReturn(true);

        // if API already return true; then we do not expect a call with for the PUBLIC visibility
        $this->projectDescriptor->isVisibilityAllowed(Argument::exact(Settings::VISIBILITY_PUBLIC))
            ->shouldNotBeCalled();

        $descriptor = $this->prophesize(MethodDescriptor::class);

        $tagsCollection = new Collection();
        $tagsCollection->set('api', new TagDescriptor('api'));
        $descriptor->getTags()->shouldBeCalled()->willReturn($tagsCollection);

        $this->assertSame($descriptor->reveal(), $this->fixture->__invoke($descriptor->reveal()));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfVisibilityIsAllowed() : void
    {
        $this->projectDescriptor->isVisibilityAllowed(Argument::exact(Settings::VISIBILITY_PUBLIC))
            ->shouldBeCalled()
            ->willReturn(true);

        $descriptor = $this->prophesize(MethodDescriptor::class);
        $descriptor->getVisibility()->shouldBeCalled()->willReturn('public');
        $descriptor->getTags()->shouldBeCalled()->willReturn(new Collection());

        $this->assertSame($descriptor->reveal(), $this->fixture->__invoke($descriptor->reveal()));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfDescriptorNotInstanceOfVisibilityInterface() : void
    {
        $this->builderMock->getProjectDescriptor()->shouldNotBeCalled();

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getTags()->shouldBeCalled()->willReturn(new Collection());

        $this->assertSame($descriptor->reveal(), $this->fixture->__invoke($descriptor->reveal()));
    }
}
