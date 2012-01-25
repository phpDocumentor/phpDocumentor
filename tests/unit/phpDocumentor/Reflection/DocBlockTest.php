<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Reflection
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

/**
 * Test class for phpDocumentor_Reflection_DocBlock.
 *
 * @category   phpDocumentor
 * @package    Reflection
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Reflection_DocBlockTest extends PHPUnit_Framework_TestCase
{
  /** @var array[] tokens returned by token_get_all */
  protected $tokens = array();

  /**
   * @var phpDocumentor_Reflection_File
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
    $this->fixture = new phpDocumentor_Reflection_File(dirname(__FILE__) . '/../../../data/DocBlockFixture.php');
    $this->fixture->process();
  }

  /**
   * Tests the EmptyDocBlock scenario.
   *
   * @return void
   */
  public function testConstruct_EmptyDocBlock()
  {
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
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
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
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
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
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
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
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
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
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
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
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

    $this->assertInstanceOf('phpDocumentor_Reflection_DocBlock_Tag_Param', current($method->getDocBlock()->getTagsByName('param')));
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
    /** @var phpDocumentor_Reflection_Class $class  */
    $class = $this->fixture->getClass('phpDocumentor_Tests_Data_DocBlockFixture');
    $this->assertInstanceOf('phpDocumentor_Reflection_Class', $class);

    /** @var phpDocumentor_Reflection_Method $method */
    $method = $class->getMethod('DocBlockWithInvalidShortDescription');

    $this->assertEquals(
      "This docblock is invalid because the short description 'does not end'",
      $method->getDocBlock()->getShortDescription()
    );
    $this->assertEquals('', trim($method->getDocBlock()->getLongDescription()->getContents()));
    $this->assertInstanceOf('phpDocumentor_Reflection_DocBlock_Tag_Param', current($method->getDocBlock()->getTagsByName('param')));
    $this->assertEquals('$object', current($method->getDocBlock()->getTagsByName('param'))->getVariableName());
    $this->assertEquals('ArrayObject', current($method->getDocBlock()->getTagsByName('param'))->getType());
  }

}