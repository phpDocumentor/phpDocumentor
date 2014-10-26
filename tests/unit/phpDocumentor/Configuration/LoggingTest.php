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

namespace phpDocumentor\Configuration;

use Psr\Log\LogLevel;

class LoggingTest extends \PHPUnit_Framework_TestCase
{
    /** @var Logging */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Logging();
    }

    /**
     * @covers phpDocumentor\Configuration\Logging::getLevel
     */
    public function testIfTheLogLevelCanBeRetrieved()
    {
        $this->assertSame(LogLevel::ERROR, $this->fixture->getLevel());
    }

    /**
     * @covers phpDocumentor\Configuration\Logging::getPaths
     */
    public function testIfTheLoggingPathsCanBeRetrieved()
    {
        $this->assertSame(array('default' => null, 'errors' => null), $this->fixture->getPaths());
    }
}
