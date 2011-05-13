<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tests
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Test class for BidirectionalArrayIterator.
 *
 * @category   DocBlox
 * @package    Tests
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
class DocBlox_BidirectionalIteratorTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_BidirectionalIterator */
  protected $fixture = null;

  /** @var string Expected serialized values */
  const SERIALIZED = 'a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:9;i:9;i:10;}';

  /**
   * Prepares the fixture for the test.
   *
   * @return void
   */
  protected function setUp()
  {
    $this->fixture = new DocBlox_BidirectionalIterator(array(
      1,2,3,4,5,6,7,8,9,10
    ));
  }

  /**
   * Test if the result of the count keyword matches the number of elements.
   *
   * @return void
   */
  public function testCount()
  {
    $this->assertEquals(10, count($this->fixture));
  }

  /**
   * Tests whether setting an existing or new values succeeds.
   *
   * @return void
   */
  public function testSet()
  {
    $this->assertEquals(10, $this->fixture[9]);
    $this->fixture[9] = 100;
    $this->assertEquals(100, $this->fixture[9]);

//    $this->setExpectedException('Exception');
//    $this->fixture[11] = 11;
  }

  /**
   * Tests whether unsetting throws an exception.
   *
   * @return void
   */
  public function testUnset()
  {
//    $this->setExpectedException('Exception');
//    unset($this->fixture[1]);
  }

  /**
   * Tests whether getting a value returns the correct one.
   *
   * @return void
   */
  public function testGet()
  {
    $this->assertEquals(2, $this->fixture[1]);
    $this->assertEquals(null, $this->fixture[11]);
  }

  /**
   * Checks whether the isset method returns the correct result.
   *
   * @return void
   */
  public function testIsset()
  {
    $this->assertEquals(true, isset($this->fixture[1]));
    $this->assertEquals(false, isset($this->fixture[11]));
  }

  /**
   * Tests whether the seek method finds the correct value or false if no item is on that location.
   *
   * @return void
   */
  public function testSeek()
  {
    $this->assertEquals(2, $this->fixture->seek(1));
    $this->assertEquals(false, $this->fixture->seek(11));
  }

  /**
   * Tests whether the key method returns the right result.
   *
   * Note: for some reason the key() keyword does not work; this has been observed in other ArrayObjects as well.
   *
   * @return void
   */
  public function testKey()
  {
    $this->assertEquals(0, $this->fixture->key());
//    $this->assertEquals(0, key($this->fixture));
    $this->fixture->next();
    $this->assertEquals(1, $this->fixture->key());
//    $this->assertEquals(1, key($this->fixture));
  }

  /**
   * Tests whether the validity check also works.
   *
   * @return void
   */
  public function testValid()
  {
    $this->assertEquals(10, $this->fixture->seek(9));
    $this->assertEquals(true, $this->fixture->valid());
    $this->assertEquals(false, $this->fixture->next());
    $this->assertEquals(false, $this->fixture->valid());
  }

  /**
   * Tests whether next returns indeed the next token and shifts the pointer; or returns false is there is no next.
   *
   * @return void
   */
  public function testNext()
  {
    $this->assertEquals(false, $this->fixture->seek(11));
    $this->assertEquals(false, $this->fixture->next());

    $this->assertEquals(9, $this->fixture->seek(8));
    $this->assertEquals(10, $this->fixture->next());
    $this->assertEquals(false, $this->fixture->next()); // out of bounds
  }

  /**
   * Tests whether previous returns indeed the previous token and shifts the pointer; or returns false is there
   * is no previous.
   *
   * @return void
   */
  public function testPrevious()
  {
    $this->assertEquals(false, $this->fixture->seek(11));
    $this->assertEquals(false, $this->fixture->previous());

    $this->assertEquals(2, $this->fixture->seek(1));
    $this->assertEquals(1, $this->fixture->previous());
    $this->assertEquals(false, $this->fixture->previous());
  }

  /**
   * Tests whether the current method returns the element contained underneath the current pointer.
   *
   * Note: for some reason the current() keyword does not work; this has been observed with other ArrayObjects.
   *
   * @return void
   */
  public function testCurrent()
  {
    $this->assertEquals(2, $this->fixture->seek(1));
//    $this->assertEquals(2, current($this->fixture));
    $this->assertEquals(2, $this->fixture->current());
  }

  /**
   * Tests whether the items are successfully serialized.
   *
   * @return void
   */
  public function testSerialize()
  {
//    $this->assertEquals(self::SERIALIZED, $this->fixture->serialize());
  }

  /**
   * Tests whether unserialization provides the right results.
   *
   * @return void
   */
  public function testUnserialize()
  {
//    $f2 = new DocBlox_BidirectionalIterator(array());
//    $f2->unserialize(self::SERIALIZED);
//    $this->assertEquals($this->fixture, $f2);
  }

}