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

namespace phpDocumentor\Application\Cli\Command;

use Mockery as m;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Tests for the phpDocumentor Command class.
 *
 * @coversDefaultClass phpDocumentor\Application\Cli\Command\Command
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var Command */
    protected $fixture;

    /**
     * Initialize a Command to test against.
     */
    protected function setUp()
    {
        $this->fixture = new Command('test');
    }

    /**
     * @covers ::setHelperSet
     */
    public function testLoggerHelperReceivesCurrentCommand()
    {
        // Arrange
        $loggerHelperMock = m::mock(HelperInterface::class);
        $loggerHelperMock->shouldReceive('addOptions')->with($this->fixture);
        $loggerHelperMock->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelperMock->shouldIgnoreMissing();
        $helperSet = new HelperSet(['phpdocumentor_logger' => $loggerHelperMock]);

        // Act
        $this->fixture->setHelperSet($helperSet);

        // Assert; Mockery does all assertions. For PHPUnit we must have an assertion
        $this->assertTrue(true);
    }

    /**
     * @covers ::getProgressBar
     */
    public function testIfProgressBarIsReturnedWhenEnabledAsOption()
    {
        // Arrange
        $loggerHelperMock = m::mock(HelperInterface::class);
        $loggerHelperMock->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelperMock->shouldIgnoreMissing();
        $progressBarHelperMock = m::mock(HelperInterface::class);
        $progressBarHelperMock->shouldReceive('getName')->andReturn('progress');
        $progressBarHelperMock->shouldIgnoreMissing();
        $this->fixture->setHelperSet(
            new HelperSet(['progress' => $progressBarHelperMock, 'phpdocumentor_logger' => $loggerHelperMock])
        );

        $inputInterface = m::mock(InputInterface::class);
        $inputInterface->shouldReceive('getOption')->with('progressbar')->andReturn(true);
        $r = new \ReflectionObject($this->fixture);
        $m = $r->getMethod('getProgressBar');
        $m->setAccessible(true);

        // Act
        $result = $m->invoke($this->fixture, $inputInterface);

        // Assert
        $this->assertSame($progressBarHelperMock, $result);
    }

    /**
     * @covers ::getProgressBar
     */
    public function testIfProgressBarIsNotReturnedWhenDisabledAsOption()
    {
        // Arrange
        $loggerHelperMock = m::mock(HelperInterface::class);
        $loggerHelperMock->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelperMock->shouldIgnoreMissing();
        $progressBarHelperMock = m::mock(HelperInterface::class);
        $progressBarHelperMock->shouldReceive('getName')->andReturn('progress');
        $progressBarHelperMock->shouldIgnoreMissing();
        $this->fixture->setHelperSet(
            new HelperSet(['progress' => $progressBarHelperMock, 'phpdocumentor_logger' => $loggerHelperMock])
        );

        $inputInterface = m::mock(InputInterface::class);
        $inputInterface->shouldReceive('getOption')->with('progressbar')->andReturn(false);
        $r = new \ReflectionObject($this->fixture);
        $m = $r->getMethod('getProgressBar');
        $m->setAccessible(true);

        // Act
        $result = $m->invoke($this->fixture, $inputInterface);

        // Assert
        $this->assertNull($result);
    }
}
