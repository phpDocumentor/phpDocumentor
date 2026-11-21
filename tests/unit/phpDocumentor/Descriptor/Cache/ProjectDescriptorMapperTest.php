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

namespace phpDocumentor\Descriptor\Cache;

use phpDocumentor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

#[CoversClass(ProjectDescriptorMapper::class)]
#[UsesClass('\phpDocumentor\Descriptor\Collection')]
#[UsesClass('\phpDocumentor\Descriptor\DescriptorAbstract')]
#[UsesClass('\phpDocumentor\Descriptor\FileDescriptor')]
#[UsesClass('\phpDocumentor\Descriptor\NamespaceDescriptor')]
#[UsesClass('\phpDocumentor\Descriptor\ProjectDescriptor')]
#[UsesClass('\phpDocumentor\ProjectSettings')]
final class ProjectDescriptorMapperTest extends TestCase
{
    use Faker;

    private ProjectDescriptorMapper $mapper;
    private FilesystemAdapter $cachePool;

    protected function setUp(): void
    {
        $this->cachePool = new FilesystemAdapter();
        $this->mapper = new ProjectDescriptorMapper($this->cachePool);
    }

    public function testThatATheSettingsForAProjectDescriptorArePersistedAndCanBeRetrievedFromCache(): void
    {
        $versionNumber = self::faker()->numerify('v#.#.#');
        $apiSetName = self::faker()->word;

        $fileDescriptor = new FileDescriptor('fileHash');
        $fileDescriptor->setPath('./src/MyClass.php');

        $projectDescriptor = new ProjectDescriptor('project');
        $restoredSet = self::faker()->apiSetDescriptor($apiSetName);
        $restoredVersion = self::faker()->versionDescriptor([$restoredSet], $versionNumber);
        $projectDescriptor->getVersions()->add($restoredVersion);
        $restoredSet->getFiles()->set('./src/MyClass.php', $fileDescriptor);

        $this->assertFalse($projectDescriptor->getSettings()->shouldIncludeSource());
        $projectDescriptor->getSettings()->includeSource();
        $this->assertTrue($projectDescriptor->getSettings()->shouldIncludeSource());

        $this->mapper->save($projectDescriptor);

        $restoredProjectDescriptor = new ProjectDescriptor('project2');
        $restoredSet = self::faker()->apiSetDescriptor($apiSetName);
        $restoredVersion = self::faker()->versionDescriptor([$restoredSet], $versionNumber);
        $restoredProjectDescriptor->getVersions()->add($restoredVersion);
        $this->mapper->populate($restoredProjectDescriptor);

        $this->assertTrue($restoredProjectDescriptor->getSettings()->shouldIncludeSource());
        $this->assertEquals($fileDescriptor, $restoredSet->getFiles()->get($fileDescriptor->getPath()));
    }
}
