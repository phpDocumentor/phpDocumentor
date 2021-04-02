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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\ApiAnalyzer;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\Debug
 * @covers ::__construct
 * @covers ::<private>
 */
final class DebugTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    /**
     * @covers ::execute
     */
    public function testLogDebugAnalysis(): void
    {
        $testString = 'test';
        $projectDescriptorMock = $this->faker()->apiSetDescriptor();

        $loggerMock = $this->prophesize(LoggerInterface::class);
        $loggerMock->debug(Argument::exact($testString))->shouldBeCalled();

        $analyzerMock = $this->prophesize(ApiAnalyzer::class);
        $analyzerMock->analyze(Argument::exact($projectDescriptorMock))->shouldBeCalled();
        $analyzerMock->__toString()->shouldBeCalled()->willReturn($testString);

        $fixture = new Debug($loggerMock->reveal(), $analyzerMock->reveal());
        $fixture->execute($projectDescriptorMock);

        $this->assertTrue(true);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription(): void
    {
        $analyzerMock = $this->prophesize(ApiAnalyzer::class);
        $debug = new Debug(new NullLogger(), $analyzerMock->reveal());

        $this->assertSame('Analyze results and write report to log', $debug->getDescription());
    }
}
