<?php

namespace phpDocumentor\Guides\Formats;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Guides\Formats\OutputFormats
 * @covers ::<private>
 */
final class OutputFormatsTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testRecordOutputFormatAndRetrieveItByItsExtension(): void
    {
        $outputFormat = $this->prophesize(OutputFormat::class);
        $outputFormat->getFileExtension()->willReturn('HTML');

        $outputFormats = new OutputFormats([$outputFormat->reveal()]);

        self::assertSame($outputFormat->reveal(), $outputFormats->get('html'));
    }

    /**
     * @covers ::add
     */
    public function testCanRegisterAndRetrieveNewFormats(): void
    {
        $outputFormat = $this->prophesize(OutputFormat::class);
        $outputFormat->getFileExtension()->willReturn('HTML');

        $outputFormats = new OutputFormats();
        $outputFormats->add($outputFormat->reveal());

        self::assertSame($outputFormat->reveal(), $outputFormats->get('html'));
    }

    /**
     * @covers ::get
     */
    public function testAnExceptionOccursWhenTryingToRetrieveAnUnknownFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $outputFormats = new OutputFormats();
        $outputFormats->get('html');
    }
}
