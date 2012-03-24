<?php

class phpDocumentor_Core_Abstract_Mock extends phpDocumentor_Core_Abstract
{

}

/**
* Test class for phpDocumentor_Core_Abstract.
*/
class phpDocumentor_Core_AbstractTest extends PHPUnit_Framework_TestCase
{
  /** @var phpDocumentor_Core_Abstract_Mock */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new phpDocumentor_Core_Abstract_Mock();
  }

  public function testGetConfig()
  {
    $config = $this->fixture->getConfig();
    $this->assertInstanceOf('phpDocumentor_Core_Config', $config);

    // loading of the template configs
    $this->assertTrue(isset($config->templates));
    $this->assertTrue(isset($config->templates->stub));
    $this->assertTrue(isset($config->templates->stub->transformations));
  }

  public function testConfig()
  {
    $this->assertInstanceOf('phpDocumentor_Core_Config', phpDocumentor_Core_Abstract::config());

  }

  public function testGetLogLevel()
  {
    // test uninitialized
    $this->assertEquals(
      constant('phpDocumentor_Core_Log::' . strtoupper($this->fixture->getConfig()->logging->level)),
      $this->fixture->getLogLevel()
    );

    $this->fixture->setLogLevel(phpDocumentor_Core_Log::ALERT);
    $this->assertEquals(phpDocumentor_Core_Log::ALERT, $this->fixture->getLogLevel());
    $this->fixture->setLogLevel(phpDocumentor_Core_Log::CRIT);
    $this->assertEquals(phpDocumentor_Core_Log::CRIT, $this->fixture->getLogLevel());
  }

  public function testSetLogLevel()
  {
    $this->fixture->setLogLevel(phpDocumentor_Core_Log::ALERT);
    $this->assertEquals(phpDocumentor_Core_Log::ALERT, $this->fixture->getLogLevel());

    $this->fixture->setLogLevel('crit');
    $this->assertEquals(phpDocumentor_Core_Log::CRIT, $this->fixture->getLogLevel());

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
