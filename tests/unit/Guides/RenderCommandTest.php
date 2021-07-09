<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class RenderCommandTest extends MockeryTestCase
{
    use ProphecyTrait;

    public function test_providing_a_destination_to_render_to() : void
    {
        $destination = $this->prophesize(FilesystemInterface::class)->reveal();

        $command = new RenderCommand(
            new Configuration('rst', []),
            $destination
        );

        self::assertSame($destination, $command->getDestination());
    }
}
