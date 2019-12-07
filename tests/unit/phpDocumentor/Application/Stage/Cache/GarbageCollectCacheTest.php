<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage\Cache;

use phpDocumentor\Application\Stage\Parser\Payload;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use PHPUnit\Framework\TestCase;

final class GarbageCollectCacheTest extends TestCase
{
    public function testItWillInstructTheDescriptorMapperToCollectGarbage() : void
    {
        $files = ['file1'];

        $descriptorMapper = $this->prophesize(ProjectDescriptorMapper::class);
        $descriptorMapper->garbageCollect($files)->shouldBeCalledOnce();

        $fixture = new GarbageCollectCache($descriptorMapper->reveal());

        $fixture(new Payload([], $this->prophesize(ProjectDescriptorBuilder::class)->reveal(), $files));
    }
}
