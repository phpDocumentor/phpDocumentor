<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Test class for DocBlox_Reflection_DocBlock.
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_DocBlockTest extends PHPUnit_Framework_TestCase
{
  /** @var array[] tokens returned by token_get_all */
  protected $tokens = array();

  /**
   * @var DocBlox_Reflection_File
   */
  protected $fixture = null;

  /**
   * Sets up the fixture.
   *
   * This method is called before a test is executed.
   *
   * @return void
   */
  protected function setUp()
  {
    $this->fixture = new DocBlox_Reflection_File(dirname(__FILE__) . '/../../../data/DocBlockFixture.php');
    $ll = $this->fixture->getLogLevel();
    $this->fixture->setLogLevel(-1);
    $this->fixture->process();
    $this->fixture->setLogLevel($ll);
  }

  /**
   * Tests the EmptyDocBlock scenario.
   *
   * @return void
   */
  public function testConstruct_EmptyDocBlock()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('EmptyDocBlock');

    $this->assertEquals('', $method->getDocBlock()->getShortDescription());
    $this->assertEquals('', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertFalse(current($method->getDocBlock()->getTagsByName('param')));
  }

  /**
   * Tests the ReallyEmptyDocBlock scenario.
   *
   * @return void
   */
  public function testConstruct_ReallyEmptyDocBlock()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('ReallyEmptyDocBlock');

    $this->assertEquals('', $method->getDocBlock()->getShortDescription());
    $this->assertEquals('', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertFalse(current($method->getDocBlock()->getTagsByName('param')));
  }

  /**
   * Tests the SingleLineDocBlock scenario.
   *
   * @return void
   */
  public function testConstruct_SingleLineDocBlock()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('SingleLineDocBlock');

    $this->assertEquals('Single line docblock.', $method->getDocBlock()->getShortDescription());
    $this->assertEquals('', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertFalse(current($method->getDocBlock()->getTagsByName('param')));
  }

  /**
   * Tests the SingleLineDocBlock2 scenario.
   *
   * @return void
   */
  public function testConstruct_SingleLineDocBlock2()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('SingleLineDocBlock2');

    $this->assertEquals('Single line docblock.', $method->getDocBlock()->getShortDescription());
    $this->assertEquals('', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertFalse(current($method->getDocBlock()->getTagsByName('param')));
  }

  /**
   * Tests the SimpleDocBlockWithLD scenario.
   *
   * @return void
   */
  public function testConstruct_SimpleDocBlockWithLD()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('SimpleDocBlockWithLD');

    $this->assertEquals('Single line docblock.', $method->getDocBlock()->getShortDescription());
    $this->assertEquals('Long description.', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertEquals('<p>Long description.</p>', trim($method->getDocBlock()->getLongDescription()->getFormattedContents()));
    $this->assertFalse(current($method->getDocBlock()->getTagsByName('param')));
  }

  /**
   * Tests the IdealDocBlock scenario.
   *
   * @return void
   */
  public function testConstruct_IdealDocBlock()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('IdealDocBlock');

    $this->assertEquals(
      "This docblock is the ideal situation, short descriptions are single line and closed with a point.",
      $method->getDocBlock()->getShortDescription()
    );

    $this->assertEquals(<<<LD
The long description is separated a whiteline away and has a trailing whiteline. After which each
tag 'group' is separated by a whiteline.
LD
      ,
      trim($method->getDocBlock()->getLongDescription()->getContents())
    );

    $this->assertEquals(<<<LD
<p>The long description is separated a whiteline away and has a trailing whiteline. After which each
tag 'group' is separated by a whiteline.</p>
LD
      ,
      trim($method->getDocBlock()->getLongDescription()->getFormattedContents())
    );

    $this->assertInstanceOf('DocBlox_Reflection_DocBlock_Tag_Param', current($method->getDocBlock()->getTagsByName('param')));
    $this->assertEquals('$object', current($method->getDocBlock()->getTagsByName('param'))->getVariableName());
    $this->assertEquals('ArrayObject', current($method->getDocBlock()->getTagsByName('param'))->getType());
  }

  /**
   * Tests the DocBlockWithInvalidShortDescription scenario.
   *
   * @return void
   */
  public function testConstruct_DocBlockWithInvalidShortDescription()
  {
    /** @var DocBlox_Reflection_Class $class  */
    $class = $this->fixture->getClass('DocBlox_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('DocBlox_Reflection_Class', $class);

    /** @var DocBlox_Reflection_Method $method */
    $method = $class->getMethod('DocBlockWithInvalidShortDescription');

    $this->assertEquals(
      "This docblock is invalid because the short description 'does not end'",
      $method->getDocBlock()->getShortDescription()
    );
    $this->assertEquals('', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertInstanceOf('DocBlox_Reflection_DocBlock_Tag_Param', current($method->getDocBlock()->getTagsByName('param')));
    $this->assertEquals('$object', current($method->getDocBlock()->getTagsByName('param'))->getVariableName());
    $this->assertEquals('ArrayObject', current($method->getDocBlock()->getTagsByName('param'))->getType());
  }

}