<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node;

class ConstantNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReferrerString()
    {
        $dom = new \DOMDocument();
        $node = new \DOMElement('constant');
        $dom->appendChild($node);
        $node->appendChild(new \DOMElement('name', 'CONST'));

        $parent = $this->getMock(
            '\phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node\ClassNode',
            array('getFQCN'), array(), '', false
        );
        $array = array();

        $parent->expects($this->once())->method('getFQCN')->will($this->returnValue('MyParent'));
        $fixture = new \phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node\ConstantNode(
            $node, $array, $parent
        );

        $this->assertEquals('MyParent::CONST', $fixture->getReferrerString());
        $this->assertEquals(
            'MyNewParent::CONST',
            $fixture->getReferrerString('MyNewParent')
        );
    }

}