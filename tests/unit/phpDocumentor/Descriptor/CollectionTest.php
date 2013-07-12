<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;

/**
 * Tests the functionality for the Collection class.
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
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
     * @covers phpDocumentor\Descriptor\Collection::offsetSet
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

    /**
     * @covers phpDocumentor\Descriptor\Collection::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetItemsWithEmptyKeyShouldThrowException()
    {
        $this->fixture->set('', 'abc');
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::offsetSet
     * @expectedException \InvalidArgumentException
     */
    public function testSetItemsUsingOffsetSetWithEmptyKeyShouldThrowException()
    {
        $this->fixture->offsetSet('', 'abc');
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::get
     * @covers phpDocumentor\Descriptor\Collection::__get
     * @covers phpDocumentor\Descriptor\Collection::offsetGet
     */
    public function testRetrievalOfItems()
    {
        $this->fixture['a'] = 'abc';
        $this->assertEquals('abc', $this->fixture->a);
        $this->assertEquals('abc', $this->fixture['a']);
        $this->assertEquals('abc', $this->fixture->get('a'));
        $this->assertCount(1, $this->fixture);

        $this->assertEquals('def', $this->fixture->get(1, 'def'));
        $this->assertCount(2, $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::getAll
     */
    public function testRetrieveAllItems()
    {
        $this->fixture['a'] = 'abc';
        $this->assertSame(array('a' => 'abc'), $this->fixture->getAll());
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::getIterator
     */
    public function testGetIterator()
    {
        $this->fixture['a'] = 'abc';
        $this->assertInstanceOf('ArrayIterator', $this->fixture->getIterator());
        $this->assertSame(array('a' => 'abc'), $this->fixture->getIterator()->getArrayCopy());
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::count
     * @covers phpDocumentor\Descriptor\Collection::offsetUnset
     */
    public function testCountReturnsTheNumberOfElements()
    {
        $this->assertCount(0, $this->fixture);
        $this->assertEquals(0, $this->fixture->count());

        $this->fixture[0] = 'abc';

        $this->assertCount(1, $this->fixture);
        $this->assertEquals(1, $this->fixture->count());

        $this->fixture[1] = 'def';

        $this->assertCount(2, $this->fixture);
        $this->assertEquals(2, $this->fixture->count());

        unset($this->fixture[0]);

        $this->assertCount(1, $this->fixture);
        $this->assertEquals(1, $this->fixture->count());
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::clear
     */
    public function testClearingTheCollection()
    {
        $this->fixture[1] = 'a';
        $this->fixture[2] = 'b';

        $this->assertCount(2, $this->fixture);

        $this->fixture->clear();

        $this->assertCount(0, $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::offsetExists
     */
    public function testIfExistingElementsAreDetected()
    {
        $this->assertFalse(isset($this->fixture[0]));
        $this->assertFalse($this->fixture->offsetExists(0));

        $this->fixture[0] = 'abc';

        $this->assertTrue(isset($this->fixture[0]));
        $this->assertTrue($this->fixture->offsetExists(0));
    }

    /**
     * @covers phpDocumentor\Descriptor\Collection::merge
     */
    public function testIfAfterMergeCollectionContainsAllItems()
    {
        $expected = array(0 => 'a', 1 => 'b', 2 => 'c');
        $this->fixture[1] = 'a';
        $this->fixture[2] = 'b';

        $collection2 = new Collection();
        $collection2[4] = 'c';

        $result = $this->fixture->merge($collection2);

        $this->assertSame($expected, $result->getAll());
    }
}
