<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Transformer\Writer\FileIo;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;

final class WriterInitializationEventTest extends \PHPUnit\Framework\TestCase
{
    private $fixture;
    private $writer;

    protected function setUp(): void
    {
        $this->fixture = new WriterInitializationEvent(new \stdClass());
        $this->writer = new FileIo();
    }

    /**
     * @covers \phpDocumentor\Transformer\Event\WriterInitializationEvent::getWriter
     * @covers \phpDocumentor\Transformer\Event\WriterInitializationEvent::setWriter
     */
    public function testSetAndGetWriter() : void
    {
        $this->assertNull($this->fixture->getWriter());

        $this->fixture->setWriter($this->writer);

        $this->assertSame($this->writer, $this->fixture->getWriter());
    }
}
