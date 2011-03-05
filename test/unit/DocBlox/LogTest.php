<?php

/**
* Test class for DocBlox_Log.
*/
class DocBlox_LogTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Log */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Log('/tmp/docblox_log_test');
  }

  public function testConstruct()
  {
    $this->assertSame('/tmp/docblox_log_test', $this->fixture->getFilename());

    $fixture = new DocBlox_Log('/docblox_log_test');
    $this->assertSame(null, $fixture->getFilename());

    // we explicitly do not check for seconds as it is not important and might go wrong when we just cross a second
    $fixture = new DocBlox_Log('/tmp/docblox_log_{DATE}');
    $this->assertStringStartsWith('/tmp/docblox_log_'.date('YmdHi'), $fixture->getFilename());

    $fixture = new DocBlox_Log('{APP_ROOT}/test_log');
    $this->assertEquals(realpath(dirname(__FILE__).'/../../../test_log'), $fixture->getFilename());
    unlink($fixture->getFilename());
  }

  public function testThreshold()
  {
    $this->markTestIncomplete();
  }

  public function testLog()
  {
    $this->markTestIncomplete();
  }

}