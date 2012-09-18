<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

require 'TestAbstract.php';

/**
 * Test for the XML Exporter's Argument exporter class.
 */
class ArgumentExporterTest extends TestAbstract
{
    /**
     * Tests whether the export method returns the correct XML representation.
     *
     * @covers phpDocumentor\Parser\Exporter\Xml\ArgumentExporter::export
     *
     * @return void
     */
    public function testExport()
    {
        $doc = new \DOMDocument();
        $child = $this->createChildXmlNode($doc);

        $this->createFixture('Argument')->export(
            $this->createParentXmlNode($doc), $this->createArgumentMock(), $child
        );

        $output = <<<OUTPUT
<child line="1"><name>name</name><default><![CDATA[default]]></default><type>string</type></child>
OUTPUT;
        $this->assertEquals($output, $child->ownerDocument->saveXML($child));
    }

    /**
     * Creates an argument mock object containing the to-be-asserted values.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createArgumentMock()
    {
        $argument = $this->getMock(
            '\phpDocumentor\Reflection\FunctionReflector\ArgumentReflector',
            array('getLineNumber', 'getName', 'getDefault', 'getType'),
            array(), '', false
        );
        $argument->expects($this->once())->method('getLineNumber')
            ->will($this->returnValue(1));
        $argument->expects($this->once())->method('getName')
            ->will($this->returnValue('name'));
        $argument->expects($this->once())->method('getType')
            ->will($this->returnValue('string'));
        $argument->expects($this->once())->method('getDefault')
            ->will($this->returnValue('default'));

        return $argument;
    }

}