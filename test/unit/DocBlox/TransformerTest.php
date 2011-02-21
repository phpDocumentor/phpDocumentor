<?php

/**
* Test class for DocBlox_Transformer.
*/
class DocBlox_TransformerTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Transformer */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Transformer();
  }

  public function testTarget()
  {
    $this->assertEquals('', $this->fixture->getTarget());

    $this->fixture->setTarget(dirname(__FILE__));
    $this->assertEquals(dirname(__FILE__), $this->fixture->getTarget());

    // only directories are accepted, not files
    $this->setExpectedException('Exception');
    $this->fixture->setTarget(__FILE__);

    // only valid directories are accepted
    $this->setExpectedException('Exception');
    $this->fixture->setTarget(dirname(__FILE__).'a');
  }

  public function testSource()
  {
    $this->assertEquals('', $this->fixture->getSource());
    touch('/tmp/test_structure.xml');

    $this->fixture->setSource('/tmp/test_structure.xml');
    $this->assertEquals('/tmp/test_structure.xml', $this->fixture->getSource());

    // directories are not allowed
    $this->setExpectedException('Exception');
    $this->fixture->setSource('/tmp');

    // unknown directories are not allowed
    $this->setExpectedException('Exception');
    $this->fixture->setSource('/tmpa');
  }

  public function testTemplate()
  {
    $this->assertSame(array(), $this->fixture->getTemplates());

    $this->fixture->setTemplate('test');
    $this->assertEquals(array('test'), $this->fixture->getTemplates());

    $this->fixture->setTemplate(array('test', 'test2'));
    $this->assertEquals(array('test', 'test2'), $this->fixture->getTemplates());
  }

  public function testTransformations()
  {
    $this->assertEquals(array(), $this->fixture->getTransformations());

    // test creation without parameters
    $this->fixture->addTransformation(array(
      'query'    => 'a',
      'writer'   => 'Xslt',
      'source'   => 'b',
      'artifact' => 'c',
    ));
    $this->assertEquals(1, count($this->fixture->getTransformations()));
    $this->assertInstanceOf('DocBlox_Transformation', reset($this->fixture->getTransformations()));
    $this->assertEquals(0, count(reset($this->fixture->getTransformations())->getParameters()));

    // test creation with parameters
    $this->fixture->addTransformation(array(
      'query'    => 'a',
      'writer'   => 'Xslt',
      'source'   => 'b',
      'artifact' => 'c',
      'parameters' => array(
        '1' => '2'
      )
    ));
    $this->assertEquals(2, count($this->fixture->getTransformations()));
    $transformations = $this->fixture->getTransformations();
    $this->assertEquals(1, count($transformations[1]->getParameters()));

    // test creation with pre-fab object
    $transformation = new DocBlox_Transformation('d', 'Xslt', 'b', 'c');
    $this->fixture->addTransformation($transformation);

    $this->assertEquals(3, count($this->fixture->getTransformations()));

    try
    {
      $this->fixture->addTransformation(array('a' => 'b'));
      $this->fail('Expected an exception to be thrown when supplying a bogus array');
    }
    catch(Exception $e)
    {
      // this is good; exception is thrown
    }

    try
    {
      $this->fixture->addTransformation('a');
      $this->fail('Expected an exception to be thrown when supplying a scalar');
    }
    catch (Exception $e)
    {
      // this is good; exception is thrown
    }

    try
    {
      $this->fixture->addTransformation(new StdClass());
      $this->fail('Expected an exception to be thrown when supplying false object');
    }
    catch (Exception $e)
    {
      // this is good; exception is thrown
    }
  }

  public function testAddTemplate()
  {
  }

  public function testExecute()
  {
  }

}