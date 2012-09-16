<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Ben Selby <bselby@plus.net>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

/**
 * Testing class for \phpDocumentor\Parser\Exporter\Xml\Xml
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that buildDeprecationsList correctly counts and adds to xml
     *
     * @covers phpDocumentor\Parser\Exporter\Xml\Xml::finalize
     *
     * @return void
     */
    public function testBuildDeprecationsListCanCorrectCount()
    {
        $count = 99;

        $parser = $this->getParserMock();

        /** @var \phpDocumentor\Parser\Exporter\Xml\Xml $exporter  */
        $exporter = $this->getMock(
            '\phpDocumentor\Parser\Exporter\Xml\Xml',
            array(
                'getNodeListForTagBasedQuery', 'buildNamespaceTree',
                'buildMarkerList', 'filterVisibility'
            ),
            array($parser)
        );

        $nodeList = new \stdClass();
        $nodeList->length = $count;

        $exporter
            ->expects($this->once())
            ->method('getNodeListForTagBasedQuery')
            ->will($this->returnValue($nodeList));

        $exporter->initialize();
        $exporter->finalize();

        $expected = new \DOMDocument('1.0', 'utf-8');
        $expected->loadXML(
            '<project version="'. \phpDocumentor\Application::VERSION
            .'" title=""><deprecated count="'.$count.'"/></project>'
        );
        $expected->formatOutput = true;

        $actual = $exporter->getDomDocument();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that buildMarkerList correctly counts and adds to xml.
     *
     * @covers phpDocumentor\Parser\Exporter\Xml\Xml::finalize
     *
     * @return void
     */
    public function testBuildMarkerListCanCorrectCount()
    {
        $count = 99;

        $parser = $this->getParserMock();

        /** @var \phpDocumentor\Parser\Exporter\Xml\Xml $exporter  */
        $exporter = $this->getMock(
            '\phpDocumentor\Parser\Exporter\Xml\Xml',
            array(
                'getNodeListForTagBasedQuery', 'buildNamespaceTree',
                'buildDeprecationList', 'filterVisibility'
            ),
            array($parser)
        );

        $nodeList = new \stdClass();
        $nodeList->length = $count;

        $exporter
            ->expects($this->exactly(2))
            ->method('getNodeListForTagBasedQuery')
            ->will($this->returnValue($nodeList));

        $exporter->initialize();
        $exporter->finalize();

        $expected = new \DOMDocument('1.0', 'utf-8');
        $expected->loadXML(
            '<project version="'. \phpDocumentor\Application::VERSION
            .'" title=""><marker count="'.$count.'">todo</marker><marker count="'
            .$count.'">fixme</marker></project>'
        );
        $expected->formatOutput = true;

        $actual = $exporter->getDomDocument();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Creates a mock of the parser object with the ignored_tags and markers set.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getParserMock()
    {
        $parser = $this->getMock(
            '\phpDocumentor\Parser\Parser',
            array('getIgnoredTags', 'getMarkers')
        );

        $parser
            ->expects($this->any())
            ->method('getIgnoredTags')
            ->will($this->returnValue(array()));

        $parser
            ->expects($this->any())
            ->method('getMarkers')
            ->will($this->returnValue(array('todo', 'fixme')));

        return $parser;
    }
}