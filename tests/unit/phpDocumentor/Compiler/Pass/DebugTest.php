<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;

/**
 * Tests the functionality for the Debug Pass
 */
class DebugTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers phpDocumentor\Compiler\Pass\Debug::__construct
     */
    public function testRegisterLoggerAndAnalyzer()
    {
        $analyzerMock = m::mock('phpDocumentor\Descriptor\ProjectAnalyzer');

        $fixture = new Debug($analyzerMock);

        $this->assertAttributeEquals($analyzerMock, 'analyzer', $fixture);
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\Debug::execute
     */
    public function testLogDebugAnalysis()
    {
        $testString            = 'test';
        $projectDescriptorMock = m::mock('phpDocumentor\Descriptor\Interfaces\ProjectInterface');

        $analyzerMock = m::mock('phpDocumentor\Descriptor\ProjectAnalyzer')
            ->shouldReceive('analyze')->with($projectDescriptorMock)
            ->shouldReceive('__toString')->andReturn($testString)
            ->getMock();

        $fixture = new Debug($analyzerMock);
        $fixture->execute($projectDescriptorMock);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\Debug::getDescription
     */
    public function testGetDescription()
    {
        $debug = new Debug(m::mock('phpDocumentor\Descriptor\ProjectAnalyzer'));
        $expected = 'Analyze results and write report to stdout';
        $this->assertSame($expected, $debug->getDescription());
    }
}
