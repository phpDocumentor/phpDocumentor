<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Event;

/**
 * Test for the PreFileEvent class.
 */
class PreFileEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var PreFileEvent $fixture */
    protected $fixture;

    /**
     * Sets up a fixture.
     */
    protected function setUp()
    {
        $this->fixture = new PreFileEvent(new \stdClass());
    }

    /**
     * @covers phpDocumentor\Parser\Event\PreFileEvent::getFile
     * @covers phpDocumentor\Parser\Event\PreFileEvent::setFile
     */
    public function testRemembersFileThatTriggersIt()
    {
        $filename = 'myfile.txt';

        $this->assertNull($this->fixture->getFile());

        $this->fixture->setFile($filename);

        $this->assertSame($filename, $this->fixture->getFile());
    }
}
