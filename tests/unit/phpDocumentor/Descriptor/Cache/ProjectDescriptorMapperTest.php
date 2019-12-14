<?php

declare(strict_types=1);

/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Cache;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper
 * @covers ::__construct
 */
final class ProjectDescriptorMapperTest extends MockeryTestCase
{
    /** @var ProjectDescriptorMapper */
    private $mapper;

    /** @var Pool */
    private $cachePool;

    protected function setUp() : void
    {
        $this->cachePool = new FilesystemAdapter();
        $this->mapper    = new ProjectDescriptorMapper($this->cachePool);
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
