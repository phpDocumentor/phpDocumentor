<?php
/**
* Test class for phpDocumentor_Reflection_Token.
*/
class phpDocumentor_TokenTest extends PHPUnit_Framework_TestCase
{
  /** @var phpDocumentor_Reflection_Token */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new phpDocumentor_Reflection_Token(array(T_STATIC, 'static', 100));
  }

  public function testGetName()
  {
    $this->assertEquals('T_STATIC', $this->fixture->getName());
  }

  public function testGetType()
  {
    $this->assertEquals(T_STATIC, $this->fixture->type);
  }

  public function testGetContent()
  {
    $this->assertEquals('static', $this->fixture->content);
  }

  public function testGetLineNumber()
  {
    $this->assertEquals(100, $this->fixture->getLineNumber());
  }
}