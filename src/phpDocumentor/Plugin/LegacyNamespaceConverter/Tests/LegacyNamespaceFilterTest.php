<?php

namespace phpDocumentor\Plugin\LegacyNamespaceConverter\Tests;

use Mockery as m;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter;

/**
 * Tests the phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter class.
 */
class LegacyNamespaceFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var LegacyNamespaceFilter */
    private $filter;

    /** @var ProjectDescriptorBuilder|m\MockInterface $builder */
    protected $builderMock;

    /**
     * Initializes the fixture and mocks any dependencies.
     *
     * @return void
     */
    public function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->filter      = new LegacyNamespaceFilter($this->builderMock);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testConvertClassNameWithUnderscoreWillBeConvertedToNamespace()
    {
        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('LegacyNamespace_ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\LegacyNamespace')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testMultiLevelLegacyNamespace()
    {
        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('LegacyNamespace_Sub_ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\LegacyNamespace\Sub')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testMixedNamespacesCanBeUnified()
    {
        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('LegacyNamespace_ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\NewNamespace');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\NewNamespace\\LegacyNamespace')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testClassNameWithNewNamespaceWillNotBeModified()
    {
        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\NewNamespace');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\NewNamespace')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testClassNameWithEmptyNamespace()
    {
        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testPrefixedNamespace()
    {
        $this->filter->setNamespacePrefix('Vendor');

        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\Vendor')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter::filter
     */
    public function testPrefixedNamespaceWithNamespacedClassWillNotBeModified()
    {
        $this->filter->setNamespacePrefix('Vendor');

        $descriptor = $this->createDescriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\')->once();

        $this->filter->filter($descriptor);

        $this->assertTrue(true);
    }

    /**
     * Creates a mocked Descriptor.
     *
     * @return m\MockInterface|DescriptorAbstract
     */
    private function createDescriptorMock()
    {
        return m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
    }
}
