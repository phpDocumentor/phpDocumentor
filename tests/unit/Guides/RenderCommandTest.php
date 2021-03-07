<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class RenderCommandTest extends MockeryTestCase
{
    public function test_providing_a_destination_to_render_to() : void
    {
        $destination = m::mock(FilesystemInterface::class);

        $command = new RenderCommand($destination);

        self::assertSame($destination, $command->getDestination());
    }
}
