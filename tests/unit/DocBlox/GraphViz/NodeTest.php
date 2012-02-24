<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @package   DocBlox\Parser\Tests
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Test for the the class representing a GraphViz node.
 *
 * @package DocBlox\Graphviz\Tests
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://docblox-project.org
 */
class DocBlox_GraphViz_NodeTest extends PHPUnit_Framework_TestCase
{
    /** @var DocBlox_GraphViz_Node */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new DocBlox_GraphViz_Node('name', 'label');
    }

    public function testCreate()
    {
        $this->markTestIncomplete('create test for GraphViz node must be written');
    }

    public function testName()
    {
        $this->markTestIncomplete('Name test for GraphViz node must be written');
    }

    public function testCall()
    {
        $this->markTestIncomplete('__call test for GraphViz node must be written');
    }

    public function testToString()
    {
        $this->markTestIncomplete('__toString test for GraphViz node must be written');
    }

}