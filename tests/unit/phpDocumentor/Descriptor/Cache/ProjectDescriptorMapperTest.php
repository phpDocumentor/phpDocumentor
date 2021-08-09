<?php

declare(strict_types=1);

/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Cache;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @uses \phpDocumentor\Descriptor\Collection
 * @uses \phpDocumentor\Descriptor\DescriptorAbstract
 * @uses \phpDocumentor\Descriptor\FileDescriptor
 * @uses \phpDocumentor\Descriptor\NamespaceDescriptor
 * @uses \phpDocumentor\Descriptor\ProjectDescriptor
 * @uses \phpDocumentor\Descriptor\ProjectDescriptor\Settings
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper
 * @covers ::__construct
 */
final class ProjectDescriptorMapperTest extends TestCase
{
    /** @var ProjectDescriptorMapper */
    private $mapper;

    /** @var FilesystemAdapter */
    private $cachePool;

    protected function setUp(): void
    {
        $this->cachePool = new FilesystemAdapter();
        $this->mapper    = new ProjectDescriptorMapper($this->cachePool);
    }

    /**
     * @covers ::save
     * @covers ::populate
     */
    public function testThatATheSettingsForAProjectDescriptorArePersistedAndCanBeRetrievedFromCache(): void
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
