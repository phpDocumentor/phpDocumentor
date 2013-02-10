<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mvriel
 * Date: 2/9/13
 * Time: 10:27 PM
 * To change this template use File | Settings | File Templates.
 */

namespace phpDocumentor\Descriptor;


class CollectionTest extends \PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Collection();
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::__construct
     */
    public function testInitialize()
    {
        $fixture = new Collection();

        $this->assertAttributeEquals(array(), 'items', $fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::__construct
     */
    public function testInitializeWithExistingArray()
    {
        $expected = array(1, 2);
        $fixture = new Collection($expected);

        $this->assertAttributeEquals($expected, 'items', $fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::add
     */
    public function testAddNewItem()
    {
        $expected          = array('abc');
        $expectedSecondRun = array('abc','def');

        $this->assertAttributeEquals(array(), 'items', $this->fixture);

        $this->fixture->add('abc');

        $this->assertAttributeEquals($expected, 'items', $this->fixture);

        $this->fixture->add('def');

        $this->assertAttributeEquals($expectedSecondRun, 'items', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::set
     */
    public function testSetItemsWithKey()
    {
        $expected          = array('z' => 'abc');
        $expectedSecondRun = array('z' => 'abc', 'y' => 'def');

        $this->assertAttributeEquals(array(), 'items', $this->fixture);

        $this->fixture->set('z', 'abc');

        $this->assertAttributeEquals($expected, 'items', $this->fixture);

        $this->fixture->set('y', 'def');

        $this->assertAttributeEquals($expectedSecondRun, 'items', $this->fixture);
    }
}