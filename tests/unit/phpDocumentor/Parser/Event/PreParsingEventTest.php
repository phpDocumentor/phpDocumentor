<?php

declare(strict_types=1);

namespace phpDocumentor\Parser\Event;

use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Event\PreParsingEvent
 * @covers ::__construct
 * @covers ::<private>
 */
final class PreParsingEventTest extends TestCase
{
    /**
     * @covers ::setFileCount
     * @covers ::getFileCount
     */
    public function testSettingAndGettingTheFileCount() : void
    {
        $event = new PreParsingEvent(new stdClass());
        $event->setFileCount(42);

        $this->assertSame(42, $event->getFileCount());
    }
}
