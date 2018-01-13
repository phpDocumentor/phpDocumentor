<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Configuration;

/**
 * Tests for the configuration directive related to file selection.
 */
class FilesTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var Files */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Files(['directory'], ['file'], ['ignore'], ['examples']);
    }

    /**
     * @covers phpDocumentor\Parser\Configuration\Files::__construct
     * @covers phpDocumentor\Parser\Configuration\Files::getExamples
     */
    public function testIfExamplesCanBeRetrieved()
    {
        $this->assertSame(['examples'], $this->fixture->getExamples());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration\Files::__construct
     * @covers phpDocumentor\Parser\Configuration\Files::getDirectories
     */
    public function testIfDirectoriesCanBeRetrieved()
    {
        $this->assertSame(['directory'], $this->fixture->getDirectories());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration\Files::__construct
     * @covers phpDocumentor\Parser\Configuration\Files::getFiles
     */
    public function testIfFilesCanBeRetrieved()
    {
        $this->assertSame(['file'], $this->fixture->getFiles());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration\Files::__construct
     * @covers phpDocumentor\Parser\Configuration\Files::getIgnore
     */
    public function testIfIgnoresCanBeRetrieved()
    {
        $this->assertSame(['ignore'], $this->fixture->getIgnore());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration\Files::isIgnoreHidden
     */
    public function testIfHiddenFilesShouldBeIgnored()
    {
        $this->assertTrue($this->fixture->isIgnoreHidden());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration\Files::isIgnoreSymlinks
     */
    public function testIfSymlinksShouldBeIgnored()
    {
        $this->assertTrue($this->fixture->isIgnoreSymlinks());
    }
}
