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

namespace phpDocumentor\Plugin\Scrybe;

/**
 * Test for the Application class of phpDocumentor Scrybe.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Application
     */
    public function testHasCorrectNameAndVersion()
    {
        $fixture = new Application();
        $this->assertEquals('phpDocumentor Scrybe', $fixture['console']->getName());
        $this->assertEquals(Application::VERSION, $fixture['console']->getVersion());
    }

    /**
     * Tests whether the application has initialized the manual:to-pdf Command.
     *
     * @covers \phpDocumentor\Plugin\Scrybe\Application
     *
     * @return void
     */
    public function testContainsToPdfCommand()
    {
        $this->markTestSkipped(
            'PDF command is currently disabled until a new implementation is done'
        );

        $fixture = new Application();
        $this->assertTrue($fixture['console']->has('manual:to-pdf'));
    }

    /**
     * Tests whether the application has initialized the manual:to-html Command.
     *
     * @covers \phpDocumentor\Plugin\Scrybe\Application
     *
     * @return void
     */
    public function testContainsToHtmlCommand()
    {
        $fixture = new Application();
        $this->assertTrue($fixture['console']->has('manual:to-html'));
    }
}
