<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node;

/**
 * Tests the Constant inheritance.
 */
class ConstantNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the referrer string is correctly retrieved.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node\ConstantNode::getReferrerString
     *
     * @return void
     */
    public function testGetReferrerString()
    {
        $dom = new \DOMDocument();
        $node = new \DOMElement('constant');
        $dom->appendChild($node);
        $node->appendChild(new \DOMElement('name', 'CONST'));

        $parent = $this->getMock(
            'phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node'
            .'\ClassNode',
            array('getFQCN'), array(), '', false
        );
        $array = array();

        $parent
            ->expects($this->once())
            ->method('getFQCN')
            ->will($this->returnValue('MyParent'));

        $fixture = new ConstantNode(
            $node, $array, $parent
        );

        $this->assertEquals('MyParent::CONST', $fixture->getReferrerString());
        $this->assertEquals(
            'MyNewParent::CONST',
            $fixture->getReferrerString('MyNewParent')
        );
    }

}