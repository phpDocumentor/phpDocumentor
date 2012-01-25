<?php

/**
* Test class for phpDocumentor_Core_Log.
*/
class phpDocumentor_Core_LogTest extends PHPUnit_Framework_TestCase
{
  /** @var phpDocumentor_Core_Log */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new phpDocumentor_Core_Log('/tmp/phpDocumentor_Core_Log_test');
  }

  public function testConstruct()
  {
    $this->assertSame('/tmp/phpDocumentor_Core_Log_test', $this->fixture->getFilename());

    $fixture = new phpDocumentor_Core_Log('/phpDocumentor_Core_Log_test');
    $this->assertSame(null, $fixture->getFilename());

    // we explicitly do not check for seconds as it is not important and might go wrong when we just cross a second
    $fixture = new phpDocumentor_Core_Log('/tmp/phpDocumentor_Core_Log_{DATE}');
    $this->assertStringStartsWith('/tmp/phpDocumentor_Core_Log_'.date('YmdHi'), $fixture->getFilename());

    $fixture = new phpDocumentor_Core_Log('{APP_ROOT}/test_log');
    $this->assertEquals(realpath(dirname(__FILE__).'/../../../../test_log'), $fixture->getFilename());
    unlink($fixture->getFilename());
  }

  public function testThreshold()
  {
    $this->fixture->setThreshold(phpDocumentor_Core_Log::ALERT);
    $this->assertEquals(phpDocumentor_Core_Log::ALERT, $this->fixture->getThreshold());

    $this->fixture->setThreshold('crit');
    $this->assertEquals(phpDocumentor_Core_Log::CRIT, $this->fixture->getThreshold());

    $this->setExpectedException('InvalidArgumentException');
    $this->fixture->setThreshold('crit2');
  }

  public function testLog()
  {
    if (file_exists('/tmp/phpDocumentor_Core_Log_test'))
    {
      unlink('/tmp/phpDocumentor_Core_Log_test');
    }

    $this->fixture = new phpDocumentor_Core_Log('/tmp/phpDocumentor_Core_Log_test');
    $this->fixture->setThreshold(phpDocumentor_Core_Log::ERR);
    $this->fixture->log('test', phpDocumentor_Core_Log::ERR);
    $this->fixture->log('test2', phpDocumentor_Core_Log::INFO);
    $result = file_get_contents('/tmp/phpDocumentor_Core_Log_test');

    $this->assertNotEmpty($result);
    $this->assertContains('test', $result);
    $this->assertNotContains('mb]:', $result, 'Should not contain debug information');
    $this->assertNotContains('test2', $result, 'Should not contain test2 as it is of a lower level');

    $this->fixture->setThreshold(phpDocumentor_Core_Log::DEBUG);
    $this->fixture->log('test3', phpDocumentor_Core_Log::INFO);
    $result = file_get_contents('/tmp/phpDocumentor_Core_Log_test');

    $this->assertContains('test3', $result);
    $this->assertContains('mb]:', $result, 'Should contain debug information when threshold is DEBUG');

    $this->fixture->log(array('test4'), phpDocumentor_Core_Log::INFO);
    $result = file_get_contents('/tmp/phpDocumentor_Core_Log_test');

    $this->assertContains('array', $result, 'The log should contain a var_dumped output');
  }

}