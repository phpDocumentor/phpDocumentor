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
    $this->assertTrue(isset($config->templates->responsive));
    $this->assertTrue(isset($config->templates->responsive->transformations));
  }

  public function testConfig()
  {
    $this->assertInstanceOf('phpDocumentor_Core_Config', phpDocumentor_Core_Abstract::config());

  }

}
