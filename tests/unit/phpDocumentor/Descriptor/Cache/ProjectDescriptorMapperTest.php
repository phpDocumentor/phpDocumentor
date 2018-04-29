<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-${YEAR} Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Cache;

use Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Cache\Storage\StorageInterface;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper
 * @covers ::__construct
 */
final class ProjectDescriptorMapperTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ProjectDescriptorMapper */
    private $mapper;

    /** @var StorageInterface */
    private $cacheDriver;

    protected function setUp()
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
