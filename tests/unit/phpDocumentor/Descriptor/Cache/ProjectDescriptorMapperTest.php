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

use phpDocumentor\Configuration\Source;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
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
 * @covers ::__construct
 */
final class ProjectDescriptorMapperTest extends TestCase
{
    use Faker;

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
    public function testThatDescriptorsCanBeRestoredFromCache(): void
    {
        $source = $this->faker()->source();
        $fileDescriptor = $this->faker()->fileDescriptor();
        $projectDescriptor = $this->createProjectStructure($source, $fileDescriptor);

        $this->mapper->save($projectDescriptor);

        $restoredProjectDescriptor = $this->createProjectStructure($source);
        $this->mapper->populate($restoredProjectDescriptor);

        self::assertEquals(
            $fileDescriptor,
            $restoredProjectDescriptor->getVersions()->get('1.0.0')->getDocumentationSets()->get(0)->getFiles()->get($fileDescriptor->getPath())
        );
    }

    private function createProjectStructure(Source $source, ?FileDescriptor $fileDescriptor = null): ProjectDescriptor
    {
        $apiDescriptor = new ApiSetDescriptor('api1', $source, 'api', $this->faker()->apiSpecification());
        if ($fileDescriptor !== null) {
            $apiDescriptor->addFile($fileDescriptor);
        }

        $versionDescriptor = new VersionDescriptor('1.0.0', new Collection([$apiDescriptor]));
        $projectDescriptor = new ProjectDescriptor('project');
        $projectDescriptor->getVersions()->add($versionDescriptor);

        return $projectDescriptor;
    }
}
