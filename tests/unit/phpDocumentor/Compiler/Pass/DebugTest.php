<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\Debug
 * @covers ::__construct
 * @covers ::<private>
 */
final class DebugTest extends MockeryTestCase
{
    /**
     * @covers ::execute
     */
    public function testLogDebugAnalysis() : void
    {
        $testString = 'test';
        $projectDescriptorMock = m::mock(ProjectDescriptor::class);

        $loggerMock = m::mock(LoggerInterface::class)
            ->shouldReceive('debug')->with($testString)
            ->getMock();

        $analyzerMock = m::mock(ProjectAnalyzer::class)
            ->shouldReceive('analyze')->with($projectDescriptorMock)
            ->shouldReceive('__toString')->andReturn($testString)
            ->getMock();

        $fixture = new Debug($loggerMock, $analyzerMock);
        $fixture->execute($projectDescriptorMock);

        $this->assertTrue(true);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription() : void
    {
        $debug = new Debug(new NullLogger(), m::mock(ProjectAnalyzer::class));

        $this->assertSame('Analyze results and write report to log', $debug->getDescription());
    }
}
