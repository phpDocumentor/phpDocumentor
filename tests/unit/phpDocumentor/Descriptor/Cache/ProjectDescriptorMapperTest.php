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
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Stash\Driver\Ephemeral;
use Stash\Pool;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper
 * @covers ::__construct
 */
final class ProjectDescriptorMapperTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ProjectDescriptorMapper */
    private $mapper;

    /**
     * @var Pool
     */
    private $cachePool;

    protected function setUp(): void
    {
        $this->cachePool = new FilesystemAdapter();
        $this->mapper = new ProjectDescriptorMapper($this->cachePool);
    }

    /**
     * @covers ::save
     * @covers ::populate
     */
    public function testThatATheSettingsForAProjectDescriptorArePersistedAndCanBeRetrievedFromCache() : void
    {
        $fileDescriptor = new FileDescriptor('fileHash');
        $fileDescriptor->setPath('./src/MyClass.php');

        $projectDescriptor = new ProjectDescriptor('project');
        $projectDescriptor->getFiles()->set('./src/MyClass.php', $fileDescriptor);

        $this->assertFalse($projectDescriptor->getSettings()->shouldIncludeSource());
        $projectDescriptor->getSettings()->includeSource();
        $this->assertTrue($projectDescriptor->getSettings()->shouldIncludeSource());

        $this->mapper->save($projectDescriptor);

        $restoredProjectDescriptor = new ProjectDescriptor('project2');
        $this->mapper->populate($restoredProjectDescriptor);

        $this->assertTrue($restoredProjectDescriptor->getSettings()->shouldIncludeSource());
        $this->assertEquals($fileDescriptor, $restoredProjectDescriptor->getFiles()->get($fileDescriptor->getPath()));
    }
}
