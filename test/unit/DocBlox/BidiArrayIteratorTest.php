<?php
/**
* Test class for DocBlox_Token.
*/
class DocBlox_BidiArrayIteratorTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_BidiArrayIterator */
  protected $fixture = null;

  const SERIALIZED = 'a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:9;i:9;i:10;}';

  protected function setUp()
  {
    $this->fixture = new DocBlox_BidiArrayIterator(array(
      1,2,3,4,5,6,7,8,9,10
    ));
  }

  public function testCount()
  {
    $this->assertEquals(10, count($this->fixture));
  }

  public function testSet()
  {
    $this->assertEquals(10, $this->fixture[9]);
    $this->fixture[9] = 100;
    $this->assertEquals(100, $this->fixture[9]);

    $this->setExpectedException('Exception');
    $this->fixture[11] = 11;
  }

  public function testUnset()
  {
    $this->setExpectedException('Exception');
    unset($this->fixture[1]);
  }

  public function testGet()
  {
    $this->assertEquals(2, $this->fixture[1]);
    $this->assertEquals(null, $this->fixture[11]);
  }

  public function testIsset()
  {
    $this->assertEquals(true, isset($this->fixture[1]));
    $this->assertEquals(false, isset($this->fixture[11]));
  }

  public function testSeek()
  {
    $this->assertEquals(2, $this->fixture->seek(1));
    $this->assertEquals(false, $this->fixture->seek(11));
  }

  public function testKey()
  {
    $this->assertEquals(0, $this->fixture->key());
//    $this->assertEquals(0, key($this->fixture));
    $this->fixture->next();
    $this->assertEquals(1, $this->fixture->key());
//    $this->assertEquals(1, key($this->fixture));
  }

  public function testValid()
  {
    $this->assertEquals(10, $this->fixture->seek(9));
    $this->assertEquals(true, $this->fixture->valid());
    $this->assertEquals(false, $this->fixture->next());
    $this->assertEquals(false, $this->fixture->valid());
  }

  public function testNext()
  {
    $this->assertEquals(false, $this->fixture->seek(11));
    $this->assertEquals(false, $this->fixture->next());

    $this->assertEquals(9, $this->fixture->seek(8));
    $this->assertEquals(10, $this->fixture->next());
    $this->assertEquals(false, $this->fixture->next()); // out of bounds
  }

  public function testPrevious()
  {
    $this->assertEquals(false, $this->fixture->seek(11));
    $this->assertEquals(false, $this->fixture->previous());

    $this->assertEquals(2, $this->fixture->seek(1));
    $this->assertEquals(1, $this->fixture->previous());
    $this->assertEquals(false, $this->fixture->previous());
  }

  public function testCurrent()
  {
    $this->assertEquals(2, $this->fixture->seek(1));
//    $this->assertEquals(2, current($this->fixture));
    $this->assertEquals(2, $this->fixture->current());
  }

  public function testSerialize()
  {
    $this->assertEquals(self::SERIALIZED, $this->fixture->serialize());
  }

  public function testUnserialize()
  {
    $f2 = new DocBlox_BidiArrayIterator(array());
    $f2->unserialize(self::SERIALIZED);
    $this->assertEquals($this->fixture, $f2);
  }

}