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
     * @covers phpDocumentor\Command\Command::getProgressBar
     */
    public function testIfProgressBarIsReturnedWhenEnabledAsOption()
    {
        // Arrange
        $progressBarHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $progressBarHelperMock->shouldReceive('getName')->andReturn('progress');
        $progressBarHelperMock->shouldIgnoreMissing();
        $this->fixture->setHelperSet(
            new HelperSet(array('progress' => $progressBarHelperMock))
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
        $progressBarHelperMock = m::mock('Symfony\Component\Console\Helper\HelperInterface');
        $progressBarHelperMock->shouldReceive('getName')->andReturn('progress');
        $progressBarHelperMock->shouldIgnoreMissing();
        $this->fixture->setHelperSet(
            new HelperSet(array('progress' => $progressBarHelperMock))
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
