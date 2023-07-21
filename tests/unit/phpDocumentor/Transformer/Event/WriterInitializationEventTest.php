<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;
use phpDocumentor\Transformer\Writer\FileIo;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Event\WriterInitializationEvent
 * @covers ::__construct
 */
final class WriterInitializationEventTest extends TestCase
{
    private WriterInitializationEvent|EventAbstract $fixture;

    private FileIo $writer;

    protected function setUp(): void
    {
        $this->fixture = new WriterInitializationEvent(new stdClass());
        $this->writer = new FileIo();
    }

    /**
     * @covers ::createInstance
     * @covers ::getSubject
     */
    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = WriterInitializationEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }

    /**
     * @covers ::getWriter
     * @covers ::setWriter
     */
    public function testSetAndGetWriter(): void
    {
        $this->assertNull($this->fixture->getWriter());

        $this->fixture->setWriter($this->writer);

        $this->assertSame($this->writer, $this->fixture->getWriter());
    }
}
