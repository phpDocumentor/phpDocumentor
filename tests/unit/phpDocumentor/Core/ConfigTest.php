<?php

/**
* Test class for phpDocumentor_Core_Config.
*/
class phpDocumentor_Core_ConfigTest extends PHPUnit_Framework_TestCase
{
  /** @var phpDocumentor_Core_Config */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new phpDocumentor_Core_Config(
        '<?xml version="1.0"?><phpdoc></phpdoc>'
    );
  }

  public function testConstruct()
  {
    $this->assertTrue(isset($this->fixture->paths));
    $this->assertEquals(
        realpath(dirname(__FILE__) . '/../../../..'),
        $this->fixture->paths->application
    );
    $this->assertEquals(
        realpath($this->fixture->paths->application . '/data'),
        $this->fixture->paths->data
    );
    $this->assertEquals(
        realpath($this->fixture->paths->data . '/templates'),
        $this->fixture->paths->templates
    );

    // test whether the templates are loaded
    $this->assertTrue(isset($this->fixture->templates));
    $this->assertTrue(isset($this->fixture->templates->stub));
  }

}
