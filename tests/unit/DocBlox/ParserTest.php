<?php

/**
* Test class for DocBlox_Parser.
*/
class DocBlox_ParserTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Parser */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Parser();
  }

  public function testForced()
  {
    // defaults to false
    $this->assertEquals(false, $this->fixture->isForced());

    $xml = new SimpleXMLElement('<project></project>');
    $xml->addAttribute('version', DocBlox_Core_Abstract::VERSION);

    $this->fixture->setExistingXml($xml->asXML());
    $this->assertEquals(false, $this->fixture->isForced());

    // if version differs, we force a rebuild
    $xml['version'] = DocBlox_Core_Abstract::VERSION.'a';
    $this->fixture->setExistingXml($xml->asXML());
    $this->assertEquals(true, $this->fixture->isForced());

    // switching back should undo the force
    $xml['version'] = DocBlox_Core_Abstract::VERSION;
    $this->fixture->setExistingXml($xml->asXML());
    $this->assertEquals(false, $this->fixture->isForced());

    // manually setting forced should result in a force
    $this->fixture->setForced(true);
    $this->assertEquals(true, $this->fixture->isForced());

    $this->fixture->setForced(false);
    $this->assertEquals(false, $this->fixture->isForced());
  }

  public function testValidate()
  {
    // defaults to false
    $this->assertEquals(false, $this->fixture->doValidation());

    $this->fixture->setValidate(true);
    $this->assertEquals(true, $this->fixture->doValidation());

    $this->fixture->setValidate(false);
    $this->assertEquals(false, $this->fixture->doValidation());
  }

  public function testMarkers()
  {
    $fixture_data = array('FIXME', 'TODO', 'DOIT');

    // default is TODO and FIXME
    $this->assertEquals(array('TODO', 'FIXME'), $this->fixture->getMarkers());

    $this->fixture->setMarkers($fixture_data);
    $this->assertEquals($fixture_data, $this->fixture->getMarkers());
  }

  public function testExistingXml()
  {
    // default is null
    $this->assertEquals(null, $this->fixture->getExistingXml());

    $this->fixture->setExistingXml('<?xml version="1.0" ?><project version="1.0"></project>');
    $this->assertInstanceOf('DOMDocument', $this->fixture->getExistingXml());
    $this->assertEquals('1.0', $this->fixture->getExistingXml()->documentElement->getAttribute('version'));
  }

// TODO: move this to a unit test for DocBlox_Parser_Files
//  public function testIgnorePatterns()
//  {
//    $fixture_data = '*/test/*';
//    $fixture_data2 = '*/test?/*';
//    $result_data = '.*\/test\/.*';
//    $result_data2 = '.*\/test.\/.*';
//
//    // default is empty
//    $this->assertEquals(array(), $this->fixture->getIgnorePatterns());
//
//    // test adding a normal glob with asterisks on both sides
//    $this->fixture->addIgnorePattern($fixture_data);
//    $this->assertEquals(array($result_data), $this->fixture->getIgnorePatterns());
//
//    // what happens if we add another one with a question mark?
//    $this->fixture->addIgnorePattern($fixture_data2);
//    $this->assertEquals(array($result_data, $result_data2), $this->fixture->getIgnorePatterns());
//
//    // what happens if we set all to an empty array
//    $this->fixture->setIgnorePatterns(array());
//    $this->assertEquals(array(), $this->fixture->getIgnorePatterns());
//
//    // what happens if we set both patterns using the setIgnorePatterns method
//    $this->fixture->setIgnorePatterns(array($fixture_data, $fixture_data2));
//    $this->assertEquals(array($result_data, $result_data2), $this->fixture->getIgnorePatterns());
//  }

  public function testPathHandling()
  {
    // default is only stripping the opening slash
    $this->assertEquals(ltrim(__FILE__, '/'), $this->fixture->getRelativeFilename(__FILE__));

    // after setting the current directory as root folder; should strip all but filename
    $this->fixture->setPath(dirname(__FILE__));
    $this->assertEquals(basename(__FILE__), $this->fixture->getRelativeFilename(__FILE__));

    // when providing a file in a lower directory it cannot parse and thus it is invalid
    $this->setExpectedException('InvalidArgumentException');
    $this->fixture->getRelativeFilename(realpath(dirname(__FILE__).'/../phpunit.xml'));
  }

  /**
   * Make sure the setter can transform string to array and set correct attribute
   *
   * @covers DocBlox_Parser::setVisibility
   *
   * @return void
   */
  public function testSetVisibilityCorrectlySetsAttribute()
  {
      $this->fixture->setVisibility('public,protected,private');
      $this->assertAttributeEquals(array('public', 'protected', 'private'), 'visibility', $this->fixture);
  }

}