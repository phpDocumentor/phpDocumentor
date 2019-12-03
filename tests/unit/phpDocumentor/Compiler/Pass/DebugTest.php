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

/**
 * Tests the functionality for the Debug Pass
 */
class DebugTest extends MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Compiler\Pass\Debug::execute
     */
    public function testLogDebugAnalysis() : void
    {
        $testString = 'test';
        $projectDescriptorMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');

        $loggerMock = m::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('debug')->with($testString)
            ->getMock();

        $analyzerMock = m::mock('phpDocumentor\Descriptor\ProjectAnalyzer')
            ->shouldReceive('analyze')->with($projectDescriptorMock)
            ->shouldReceive('__toString')->andReturn($testString)
            ->getMock();

        $fixture = new Debug($loggerMock, $analyzerMock);
        $fixture->execute($projectDescriptorMock);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\Debug::getDescription
     */
    public function testGetDescription() : void
    {
        $debug = new Debug(m::mock('Psr\Log\LoggerInterface'), m::mock('phpDocumentor\Descriptor\ProjectAnalyzer'));
        $expected = 'Analyze results and write report to log';
        $this->assertSame($expected, $debug->getDescription());
    }
}
