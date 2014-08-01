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

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors;

/**
 * Test class for the Discovery Visitor.
 */
class DiscoverTest extends \PHPUnit_Framework_TestCase
{
    const FILENAME = 'test';

    protected $document = null;
    protected $converter = null;
    protected $table_of_contents = null;

    /**
     * @var Discover
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Discover($this->getDocumentMock(), '');
    }

    protected function getDocumentMock()
    {
        if (!$this->document) {

            $file = $this->getMock(
                '\phpDocumentor\Fileset\File',
                array('getFilename'),
                array(),
                '',
                false
            );
            $file->expects($this->any())->method('getFilename')
                ->will($this->returnValue(self::FILENAME));

            $this->document = $this->getMock(
                '\phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Document',
                array('getConverter', 'getFile'),
                array(),
                '',
                false
            );
            $this->document->expects($this->any())
                ->method('getConverter')
                ->will($this->returnValue($this->getConverterMock()));
            $this->document->expects($this->any())
                ->method('getFile')->will($this->returnValue($file));
        }

        return $this->document;
    }

    protected function getConverterMock()
    {
        if (!$this->converter) {
            $this->converter = $this->getMock(
                '\phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\ToHtml',
                array('getTableOfContents'),
                array(),
                '',
                false
            );
            $this->converter->expects($this->any())
                ->method('getTableOfContents')
                ->will($this->returnValue($this->getTableOfContentsMock()));
        }

        return $this->converter;
    }

    protected function getTableOfContentsMock()
    {
        if (!$this->table_of_contents) {
            $this->table_of_contents = $this->getMock(
                '\phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents'
            );
        }

        return $this->table_of_contents;
    }

    public function testRetrieveTableOfContents()
    {
        $this->assertSame(
            $this->getTableOfContentsMock(),
            $this->object->getTableOfContents()
        );
    }

    public function testRetrieveFilename()
    {
        $this->markTestIncomplete("testRetrieveFilename() is not working...");
        //$this->assertSame(self::FILENAME, $this->object->getFilename());
    }

    public function testRetrieveDocument()
    {
        $this->assertSame(
            $this->getDocumentMock(),
            $this->object->getDocument()
        );
    }
}
