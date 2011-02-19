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
    $this->assertInstanceOf('Zend_Config_Xml', $this->fixture->getConfig());
  }

  public function testConfig()
  {
    $this->assertInstanceOf('Zend_Config_Xml', DocBlox_Abstract::config());
  }

  public function testLoadConfig()
  {
    DocBlox_Abstract::loadConfig(dirname(__FILE__) . '/../../../docblox.config.xml');
    $this->assertInstanceOf('Zend_Config_Xml', DocBlox_Abstract::config());

    $this->setExpectedException('Exception');
    DocBlox_Abstract::loadConfig('blabla');
  }

  public function testGetLogLevel()
  {
    // test uninitialized
    $this->assertEquals(
      constant('Zend_Log::' . strtoupper($this->fixture->getConfig()->logging->level)),
      $this->fixture->getLogLevel()
    );

    $this->fixture->setLogLevel(Zend_Log::ALERT);
    $this->assertEquals(Zend_Log::ALERT, $this->fixture->getLogLevel());
    $this->fixture->setLogLevel(Zend_Log::CRIT);
    $this->assertEquals(Zend_Log::CRIT, $this->fixture->getLogLevel());
  }

  public function testSetLogLevel()
  {
    $this->fixture->setLogLevel(Zend_Log::ALERT);
    $this->assertEquals(Zend_Log::ALERT, $this->fixture->getLogLevel());

    $this->fixture->setLogLevel('crit');
    $this->assertEquals(Zend_Log::CRIT, $this->fixture->getLogLevel());

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