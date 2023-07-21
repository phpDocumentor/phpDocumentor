<?php

declare(strict_types=1);

namespace phpDocumentor\Parser\Event;

use phpDocumentor\Event\EventAbstract;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Event\PreParsingEvent
 * @covers ::__construct
 * @covers ::<private>
 */
final class PreParsingEventTest extends TestCase
{
    private ?EventAbstract $fixture = null;

    /**
     * @covers ::createInstance
     * @covers ::getSubject
     */
    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = PreParsingEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }

    /**
     * @covers ::setFileCount
     * @covers ::getFileCount
     */
    public function testSettingAndGettingTheFileCount(): void
    {
        $event = new PreParsingEvent(new stdClass());
        $event->setFileCount(42);

        $this->assertSame(42, $event->getFileCount());
    }
}
