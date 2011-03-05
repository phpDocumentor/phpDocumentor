<?php

class DocBlox_Abstract_Mock extends DocBlox_Abstract
{

}

/**
* Test class for DocBlox_Abstract.
*/
class DocBlox_AbstractTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Abstract_Mock */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Abstract_Mock();
  }

  public function testGetConfig()
  {
    $config = $this->fixture->getConfig();
    $this->assertInstanceOf('DocBlox_Config', $config);

    // loading of the template configs
    $this->assertTrue(isset($config->templates));
    $this->assertTrue(isset($config->templates->default));
    $this->assertTrue(isset($config->templates->default->transformations));
  }

  public function testConfig()
  {
    $this->assertInstanceOf('DocBlox_Config', DocBlox_Abstract::config());

  }

  public function testGetLogLevel()
  {
    // test uninitialized
    $this->assertEquals(
      constant('DocBlox_Log::' . strtoupper($this->fixture->getConfig()->logging->level)),
      $this->fixture->getLogLevel()
    );

    $this->fixture->setLogLevel(DocBlox_Log::ALERT);
    $this->assertEquals(DocBlox_Log::ALERT, $this->fixture->getLogLevel());
    $this->fixture->setLogLevel(DocBlox_Log::CRIT);
    $this->assertEquals(DocBlox_Log::CRIT, $this->fixture->getLogLevel());
  }

  public function testSetLogLevel()
  {
    $this->fixture->setLogLevel(DocBlox_Log::ALERT);
    $this->assertEquals(DocBlox_Log::ALERT, $this->fixture->getLogLevel());

    $this->fixture->setLogLevel('crit');
    $this->assertEquals(DocBlox_Log::CRIT, $this->fixture->getLogLevel());

    $this->setExpectedException('InvalidArgumentException');
    $this->fixture->setLogLevel('crit2');
  }

  public function testLog()
  {
    $this->markTestIncomplete();
  }

  public function testDebug()
  {
    $this->markTestIncomplete();
  }

}