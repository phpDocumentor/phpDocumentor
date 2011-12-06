<?php

/**
* Test class for DocBlox_Core_Config.
*/
class DocBlox_Core_ConfigTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Core_Config */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Core_Config('<?xml version="1.0"?><docblox></docblox>');
  }

  public function testConstruct()
  {
    $this->assertTrue(isset($this->fixture->paths));
    $this->assertEquals(realpath(dirname(__FILE__) . '/../../../..'), $this->fixture->paths->application);
    $this->assertEquals(realpath($this->fixture->paths->application . '/data'), $this->fixture->paths->data);
    $this->assertEquals(realpath($this->fixture->paths->data . '/templates'), $this->fixture->paths->templates);

    // test whether the templates are loaded
    $this->assertTrue(isset($this->fixture->templates));
    $this->assertTrue(isset($this->fixture->templates->new_black));
  }

}