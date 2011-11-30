<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

class DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_ConstantTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetReferrerString()
    {
        $dom = new DOMDocument();
        $node = new DOMElement('constant');
        $dom->appendChild($node);
        $node->appendChild(new DOMElement('name', 'CONST'));

        $parent = $this->getMock(
            'DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Class',
            array('getFQCN'), array(), '', false
        );
        $array = array();

        $parent->expects($this->once())->method('getFQCN')->will($this->returnValue('MyParent'));
        $fixture = new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit_Node_Constant(
            $node, $array, $parent
        );

        $this->assertEquals('MyParent::CONST', $fixture->getReferrerString());
        $this->assertEquals(
            'MyNewParent::CONST',
            $fixture->getReferrerString('MyNewParent')
        );
    }

}