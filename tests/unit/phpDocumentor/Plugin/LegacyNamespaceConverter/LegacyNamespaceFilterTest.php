<?php

namespace phpDocumentor\Plugin\LegacyNamespaceConverter;
use Mockery as m;

class LegacyNamespaceFilterTest extends \PHPUnit_Framework_TestCase {

    /** @var  LegacyNamespaceFilter */
    private $filter;

    /** @var ProjectDescriptorBuilder $builder */
    protected $builder;

    public function setUp()
    {
        parent::setUp();

        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->filter = new LegacyNamespaceFilter($this->builderMock);
    }


    public function testConvertClassNameWithUnderscoreWillBeConvertedToNamespace()
    {
        $descriptor = $this->descriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('LegacyNamespace_ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\LegacyNamespace')->once();
        $this->filter->filter($descriptor);
        $this->assertTrue(true);
    }



    public function testMultiLevelLegacyNamespace()
    {
        $descriptor = $this->descriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('LegacyNamespace_Sub_ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\LegacyNamespace\Sub')->once();
        $this->filter->filter($descriptor);
        $this->assertTrue(true);
    }


    public function testMixedNamespacesCanBeUnified()
    {
        $descriptor = $this->descriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('LegacyNamespace_ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\NewNamespace');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\NewNamespace\\LegacyNamespace')->once();
        $this->filter->filter($descriptor);
        $this->assertTrue(true);
    }


    public function testClassNameWithNewNamespaceWillNotBeModified()
    {
        $descriptor = $this->descriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\NewNamespace');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\NewNamespace')->once();
        $this->filter->filter($descriptor);
        $this->assertTrue(true);
    }


    public function testClassNameWithEmptyNamespace()
    {
        $descriptor = $this->descriptorMock();
        $descriptor->shouldReceive('getName')->andReturn('ClassName');
        $descriptor->shouldReceive('getNamespace')->andReturn('\\');

        $descriptor->shouldReceive('setName')->with('ClassName')->once();
        $descriptor->shouldReceive('setNamespace')->with('\\')->once();
        $this->filter->filter($descriptor);
        $this->assertTrue(true);
    }


    private function descriptorMock()
    {
        return m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
    }

}
