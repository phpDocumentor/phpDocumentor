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
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

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
     * @dataProvider removedInternalTagProvider
     */
    public function testStripsInternalTagFromDescription(string $inputDescription, string $output) : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);
        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($inputDescription);
        $this->assertSame($output, $this->fixture->__invoke($descriptor)->getDescription());
    }

    /**
     * @return array<string, string[]>
     */
    public function removedInternalTagProvider() : array
    {
        return [
            'description with inline internaltag' => [
                'without {@internal blabla }internal tag',
                'without internal tag',
            ],
// Bug: https://github.com/phpDocumentor/ReflectionDocBlock/issues/255
//            'internal tag with braces' => [
//                'without {@internal bla{bla} }internal tag',
//                'without internal tag',
//            ],
            'legacy format internal tags' => [
                'without {@internal blabla }}internal tag',
                'without internal tag',
            ],
            'legacy format with braces' => [
                'without {@internal bla{bla} }}internal tag',
                'without internal tag',
            ],
        ];
    }

    /**
     * @uses \phpDocumentor\Descriptor\ClassDescriptor
     *
     * @covers ::__invoke
     * @dataProvider resolvedInternalTagProvider
     */
    public function testResolvesInternalTagFromDescription(string $inputDescription, string $output) : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);
        $descriptor = new ClassDescriptor();
        $descriptor->setDescription($inputDescription);
        $this->assertSame($output, $this->fixture->__invoke($descriptor)->getDescription());
    }

    /**
     * @return array<string, string[]>
     */
    public function resolvedInternalTagProvider() : array
    {
        return [
            'description with inline internaltag' => [
                'without {@internal blabla }internal tag',
                'without blabla internal tag',
            ],
            'internal tag with braces' => [
                'without {@internal bla{bla} }internal tag',
                'without bla{bla }internal tag',
            ],
            'legacy format internal tags' => [
                'without {@internal bla{bla} }}internal tag',
                'without bla{bla} internal tag',
            ],
            'legacy format with braces' => [
                'without {@internal bla{bla} }}internal tag',
                'without bla{bla} internal tag',
            ],
        ];
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
