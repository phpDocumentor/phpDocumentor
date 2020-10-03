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

use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\Debug
 * @covers ::__construct
 * @covers ::<private>
 */
final class DebugTest extends TestCase
{
    /**
     * @covers ::execute
     */
    public function testLogDebugAnalysis() : void
    {
        $testString = 'test';
        $projectDescriptorMock = $this->createMock(ProjectDescriptor::class);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->atLeastOnce())
            ->method('debug')
            ->with($testString);

        $analyzerMock = $this->createMock(ProjectAnalyzer::class);
        $analyzerMock->expects($this->atLeastOnce())
            ->method('analyze')
            ->with($projectDescriptorMock);
        $analyzerMock->expects($this->atLeastOnce())
            ->method('__toString')
            ->willReturn($testString);

        $fixture = new Debug($loggerMock, $analyzerMock);
        $fixture->execute($projectDescriptorMock);

        $this->assertTrue(true);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription() : void
    {
        $debug = new Debug(new NullLogger(), $this->createMock(ProjectAnalyzer::class));

        $this->assertSame('Analyze results and write report to log', $debug->getDescription());
    }
}
