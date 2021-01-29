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
use phpDocumentor\Pipeline\Stage\Parser\Payload;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Cache\GarbageCollectCache
 * @covers ::__construct
 * @covers ::<private>
 */
final class GarbageCollectCacheTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers ::__invoke
     */
    public function testItWillInstructTheDescriptorMapperToCollectGarbage() : void
    {
        $files = ['file1'];

        $descriptorMapper = $this->prophesize(ProjectDescriptorMapper::class);
        $descriptorMapper->garbageCollect($files)->shouldBeCalledOnce();

        $fixture = new GarbageCollectCache($descriptorMapper->reveal());

        $fixture(new Payload([], $this->prophesize(ProjectDescriptorBuilder::class)->reveal(), $files));
    }
}
