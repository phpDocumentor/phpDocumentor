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

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata;

/**
 * Test class for the Table of Contents.
 */
class TableOfContentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TableOfContents
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TableOfContents;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidFile()
    {
        $this->object[] = new TableOfContents\Heading();
    }

    public function testAdd()
    {
        $file = new TableOfContents\File();
        $file->setFilename('test');

        $this->object[] = $file;

        $this->assertCount(1, $this->object);
        $this->assertTrue(isset($this->object['test']));
        $this->assertSame($file, $this->object['test']);
    }

    public function testRecognizesModule()
    {
        $file = new TableOfContents\File();
        $file->setFilename('test');

        $file2 = new TableOfContents\File();
        $file2->setFilename('test2');

        $file3 = new TableOfContents\File();
        $file3->setFilename('index');

        $file4 = new TableOfContents\File();
        $file4->setFilename('INDEX');

        $file5 = new TableOfContents\File();
        $file5->setFilename('test3');

        $this->object[] = $file;
        $this->object[] = $file2;
        $this->object[] = $file3;
        $this->object[] = $file4;

        $this->assertCount(2, $this->object->getModules());
        $this->assertSame(array($file3, $file4), $this->object->getModules());
    }
}
