<?php

class DocBlox_Core_Abstract_Mock extends DocBlox_Core_Abstract
{

}

/**
* Test class for DocBlox_Core_Abstract.
*/
class DocBlox_Core_AbstractTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Core_Abstract_Mock */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Core_Abstract_Mock();
  }

  public function testGetConfig()
  {
    $config = $this->fixture->getConfig();
    $this->assertInstanceOf('DocBlox_Core_Config', $config);

    // loading of the template configs
    $this->assertTrue(isset($config->templates));
    $this->assertTrue(isset($config->templates->new_black));
    $this->assertTrue(isset($config->templates->new_black->transformations));
  }

  public function testConfig()
  {
    $this->assertInstanceOf('DocBlox_Core_Config', DocBlox_Core_Abstract::config());

  }

  public function testGetLogLevel()
  {
    // test uninitialized
    $this->assertEquals(
      constant('DocBlox_Core_Log::' . strtoupper($this->fixture->getConfig()->logging->level)),
      $this->fixture->getLogLevel()
    );

    $this->fixture->setLogLevel(DocBlox_Core_Log::ALERT);
    $this->assertEquals(DocBlox_Core_Log::ALERT, $this->fixture->getLogLevel());
    $this->fixture->setLogLevel(DocBlox_Core_Log::CRIT);
    $this->assertEquals(DocBlox_Core_Log::CRIT, $this->fixture->getLogLevel());
  }

  public function testSetLogLevel()
  {
    $this->fixture->setLogLevel(DocBlox_Core_Log::ALERT);
    $this->assertEquals(DocBlox_Core_Log::ALERT, $this->fixture->getLogLevel());

    $this->fixture->setLogLevel('crit');
    $this->assertEquals(DocBlox_Core_Log::CRIT, $this->fixture->getLogLevel());

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