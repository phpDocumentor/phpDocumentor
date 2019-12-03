<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Transformer\Writer\FileIo;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Event\WriterInitializationEvent
 */
final class WriterInitializationEventTest extends TestCase
{
    /** @var WriterInitializationEvent */
    private $fixture;

    /** @var FileIo */
    private $writer;

    protected function setUp() : void
    {
        $this->fixture = new WriterInitializationEvent(new stdClass());
        $this->writer = new FileIo();
    }

    /**
     * @covers ::getWriter
     * @covers ::setWriter
     */
    public function testSetAndGetWriter() : void
    {
        $this->assertNull($this->fixture->getWriter());

        $this->fixture->setWriter($this->writer);

        $this->assertSame($this->writer, $this->fixture->getWriter());
    }
}
