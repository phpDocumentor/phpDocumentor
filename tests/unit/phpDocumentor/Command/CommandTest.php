<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Command;

use Symfony\Component\Console\Helper\HelperSet;
use Mockery as m;

/**
 * Tests for the phpDocumentor Command class.
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
     * @covers phpDocumentor\Command\Command::setHelperSet
     */
    public function testLoggerHelperReceivesCurrentCommand()
    {
        // Arrange
        $loggerHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $loggerHelperMock->shouldReceive('addOptions')->with($this->fixture);
        $loggerHelperMock->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelperMock->shouldIgnoreMissing();
        $helperSet = new HelperSet(array('phpdocumentor_logger' => $loggerHelperMock));

        // Act
        $this->fixture->setHelperSet($helperSet);

        // Assert; Mockery does all assertions. For PHPUnit we must have an assertion
        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Command\Command::getProgressBar
     */
    public function testIfProgressBarIsReturnedWhenEnabledAsOption()
    {
        // Arrange
        $loggerHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $loggerHelperMock->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelperMock->shouldIgnoreMissing();
        $progressBarHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $progressBarHelperMock->shouldReceive('getName')->andReturn('progress');
        $progressBarHelperMock->shouldIgnoreMissing();
        $this->fixture->setHelperSet(
            new HelperSet(array('progress' => $progressBarHelperMock, 'phpdocumentor_logger' => $loggerHelperMock))
        );

        $inputInterface = m::mock('Symfony\Component\Console\Input\InputInterface');
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
     * @covers phpDocumentor\Command\Command::getProgressBar
     */
    public function testIfProgressBarIsNotReturnedWhenDisabledAsOption()
    {
        // Arrange
        $loggerHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $loggerHelperMock->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelperMock->shouldIgnoreMissing();
        $progressBarHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $progressBarHelperMock->shouldReceive('getName')->andReturn('progress');
        $progressBarHelperMock->shouldIgnoreMissing();
        $this->fixture->setHelperSet(
            new HelperSet(array('progress' => $progressBarHelperMock, 'phpdocumentor_logger' => $loggerHelperMock))
        );

        $inputInterface = m::mock('Symfony\Component\Console\Input\InputInterface');
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
