<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Ben Selby <bselby@plus.net>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
/**
 * Testing class for phpDocumentor_Parser_Exporter_Xml
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Ben Selby <bselby@plus.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Parser_Exported_XmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that buildDeprecationsList correctly counts and adds to xml
     *
     * @return type
     */
    public function testBuildDeprecationsListCanCorrectCount()
    {
        $count = 99;

        $parser = $this->getMock(
            'phpDocumentor_Parser',
            array('getIgnoredTags'),
            array()
        );

        $parser->expects($this->any())
               ->method('getIgnoredTags')
               ->will($this->returnValue(array()));

        $exporter = $this->getMock(
            'phpDocumentor_Parser_Exporter_Xml',
            array('getNodeListForTagBasedQuery', 'buildNamespaceTree', 'buildMarkerList', 'filterVisibility'),
            array($parser)
        );

        $nodeList = new stdClass();
        $nodeList->length = $count;

        $exporter->expects($this->once())
                 ->method('getNodeListForTagBasedQuery')
                 ->will($this->returnValue($nodeList));

        $exporter->initialize();
        $exporter->finalize();

        $expected = new DOMDocument('1.0', 'utf-8');
        $expected->loadXML('<project version="2.0.0a1" title=""><deprecated count="'.$count.'"/></project>');
        $expected->formatOutput = true;

        $actual = $exporter->getDomDocument();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that buildMarkerList correctly counts and adds to xml
     *
     * @return type
     */
    public function testBuildMarkerListCanCorrectCount()
    {
        $count = 99;

        $parser = $this->getMock(
            'phpDocumentor_Parser',
            array('getIgnoredTags', 'getMarkers'),
            array()
        );

        $parser->expects($this->any())
               ->method('getIgnoredTags')
               ->will($this->returnValue(array()));

        $parser->expects($this->any())
               ->method('getMarkers')
               ->will($this->returnValue(array('todo', 'fixme')));

        $exporter = $this->getMock(
            'phpDocumentor_Parser_Exporter_Xml',
            array('getNodeListForTagBasedQuery', 'buildNamespaceTree', 'buildDeprecationList', 'filterVisibility'),
            array($parser)
        );

        $nodeList = new stdClass();
        $nodeList->length = $count;

        $exporter->expects($this->exactly(2))
                 ->method('getNodeListForTagBasedQuery')
                 ->will($this->returnValue($nodeList));

        $exporter->initialize();
        $exporter->finalize();

        $expected = new DOMDocument('1.0', 'utf-8');
        $expected->loadXML('<project version="2.0.0a1" title=""><marker count="'.$count.'">todo</marker><marker count="'.$count.'">fixme</marker></project>');
        $expected->formatOutput = true;

        $actual = $exporter->getDomDocument();

        $this->assertEquals($expected, $actual);
    }
}