<?php

namespace phpDocumentor\Descriptor\Cache;

use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\TestCase;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Cache\Storage\StorageInterface;
use Mockery as m;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper
 * @covers ::__construct
 */
final class ProjectDescriptorMapperTest extends TestCase
{
    /** @var ProjectDescriptorMapper */
    private $mapper;

    /** @var StorageInterface */
    private $cacheDriver;

    public function setUp()
    {
        $this->cacheDriver = new Memory();
        $this->mapper = new ProjectDescriptorMapper($this->cacheDriver);
    }

    /**
     * @covers ::getCache
     */
    public function testThatTheCacheDriverCanBeRetrieved()
    {
        $this->assertSame($this->cacheDriver, $this->mapper->getCache());
    }

    /**
     * @covers ::getCache
     */
    public function testThatACacheDriverMustBeAZendStorageInterface()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->mapper = new ProjectDescriptorMapper(m::mock(StorageInterface::class));
    }

    /**
     * @covers ::save
     * @covers ::populate
     */
    public function testThatATheSettingsForAProjectDescriptorArePersistedAndCanBeRetrievedFromCache()
    {
        $projectDescriptor = new ProjectDescriptor('project');

        $this->assertFalse($projectDescriptor->getSettings()->shouldIncludeSource());
        $projectDescriptor->getSettings()->includeSource();
        $this->assertTrue($projectDescriptor->getSettings()->shouldIncludeSource());

        $this->mapper->save($projectDescriptor);

        $restoredProjectDescriptor = new ProjectDescriptor('project2');
        $this->mapper->populate($restoredProjectDescriptor);

        $this->assertTrue($restoredProjectDescriptor->getSettings()->shouldIncludeSource());
    }
}
