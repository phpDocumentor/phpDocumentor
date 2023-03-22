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

namespace phpDocumentor\Pipeline\Stage\Cache;

use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Pipeline\Stage\Parser\ApiSetPayload;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Cache\GarbageCollectCache
 * @covers ::__construct
 * @covers ::<private>
 */
final class GarbageCollectCacheTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /**
     * @covers ::__invoke
     */
    public function testItWillInstructTheDescriptorMapperToCollectGarbage(): void
    {
        $files = ['file1'];

        $descriptorMapper = $this->prophesize(ProjectDescriptorMapper::class);

        $fixture = new GarbageCollectCache($descriptorMapper->reveal());

        $builder = $this->prophesize(ProjectDescriptorBuilder::class)->reveal();
        $apiSet = $this->faker()->apiSetDescriptor();
        $version = $this->faker()->versionDescriptor([$apiSet]);

        $descriptorMapper->garbageCollect($version, $apiSet, $files)->shouldBeCalledOnce();

        $fixture(new ApiSetPayload([], $builder, $version, $apiSet, $files));
    }
}
