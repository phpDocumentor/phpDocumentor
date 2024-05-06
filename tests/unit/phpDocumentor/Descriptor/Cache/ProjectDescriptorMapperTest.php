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
use phpDocumentor\Faker\Faker;
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
 */
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
