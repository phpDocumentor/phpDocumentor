<?php
require_once 'PHPUnit/Framework.php';

set_include_path(get_include_path(). PATH_SEPARATOR .
  realpath(dirname(__FILE__) . '/../../..') . PATH_SEPARATOR .
  realpath(dirname(__FILE__) . '/../../../lib'));
require_once('Zend/Loader/Autoloader.php');
require_once('symfony/sfTimer.class.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

/**
 * Test class for DocBlox_TokenIterator.
 */
class DocBlox_TokenIteratorTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var DocBlox_TokenIterator
   */
  protected $object;

  /**
   * Sets up the fixture.
   *
   * This method is called before a test is executed.
   *
   * @return void
   */
  protected function setUp()
  {
    $tokens = token_get_all(file_get_contents(dirname(__FILE__) . '/../../data/test.php'));
    $this->object = new DocBlox_TokenIterator($tokens);

    $this->assertGreaterThan(0, count($this->object), 'Expected DocBlox_TokenIterator to contain more than 0 items');
    $this->assertEquals(count($tokens), count($this->object), 'Expected DocBlox_TokenIterator to contain the same amount of items as the output of the tokenizer');

    foreach($this->object as $token)
    {
      if (!($token instanceof DocBlox_Token))
      {
        $this->fail('All tokens in the DocBlox_TokenIterator are expected to be of type DocBlox_Token, found: '.print_r($token, true));
      }
    }

    $this->object->seek(0);
  }

  /**
   * Tests the gotoNextByType method
   */
  public function testGotoNextByType()
  {
    $this->object->seek(0);
    try
    {
      $token = $this->object->gotoNextByType(T_CLASS, -1);
      $this->fail('Expected an InvalidArgumentException when passing a negative number for the max_count argument');
    } catch (InvalidArgumentException $e)
    {
    }

    $this->object->seek(0);
    $token = $this->object->gotoNextByType(T_CLASS, 0);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset');
    $this->assertNotEquals(0, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek(0);
    $token = $this->object->gotoNextByType(T_CLASS, 40);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens');
    $this->assertNotEquals(0, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek(0);
    $token = $this->object->gotoNextByType(T_CLASS, 40, T_REQUIRE);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens before a T_REQUIRE is encountered');
    $this->assertNotEquals(0, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek(0);
    $token = $this->object->gotoNextByType(T_CLASS, 40, T_NAMESPACE);
    $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within 40 tokens before a T_NAMESPACE is encountered');
    $this->assertEquals(0, $this->object->key(), 'Expected the key to be at the starting position');
  }

  /**
   * Tests the gotoNextByType method
   */
  public function testGotoPreviousByType()
  {
    $pos = 40;

    $this->object->seek($pos);
    $token = $this->object->gotoPreviousByType(T_CLASS, 0);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset');
    $this->assertNotEquals($pos, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek($pos);
    $token = $this->object->gotoPreviousByType(T_CLASS, $pos);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within '.$pos.' tokens');
    $this->assertNotEquals($pos, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek($pos);
    $token = $this->object->gotoPreviousByType(T_CLASS, $pos, T_NAMESPACE);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within '.$pos.' tokens before a T_NAMESPACE is encountered');
    $this->assertNotEquals($pos, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek($pos);
    $token = $this->object->gotoPreviousByType(T_CLASS, $pos, T_FUNCTION);
    $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within '.$pos.' tokens before a T_FUNCTION is encountered');
    $this->assertEquals($pos, $this->object->key(), 'Expected the key to be at the starting position');
  }

  public function testFindNextByType()
  {
    $this->object->seek(0);
    try
    {
      $token = $this->object->findNextByType(T_CLASS, -1);
      $this->fail('Expected an InvalidArgumentException when passing a negative number for the max_count argument');
    } catch (InvalidArgumentException $e)
    {
    }

    $this->object->seek(0);
    $token = $this->object->findNextByType(T_CLASS, 0);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset');
    $this->assertEquals(0, $this->object->key(), 'Expected the key to equal the starting position');

    $this->object->seek(0);
    $token = $this->object->findNextByType(T_CLASS, 40);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens');
    $this->assertEquals(0, $this->object->key(), 'Expected the key to equal the starting position');

    $this->object->seek(0);
    $token = $this->object->findNextByType(T_CLASS, 40, T_REQUIRE);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens before a T_REQUIRE is encountered');
    $this->assertEquals(0, $this->object->key(), 'Expected the key to equal the starting position');

    $this->object->seek(0);
    $token = $this->object->findNextByType(T_CLASS, 40, T_NAMESPACE);
    $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within 40 tokens before a T_NAMESPACE is encountered');
    $this->assertEquals(0, $this->object->key(), 'Expected the key to be at the starting position');
  }

  public function testFindPreviousByType()
  {
    $pos = 40;

    $this->object->seek($pos);
    $token = $this->object->findPreviousByType(T_CLASS, 0);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset');
    $this->assertEquals($pos, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek($pos);
    $token = $this->object->findPreviousByType(T_CLASS, $pos);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within '.$pos.' tokens');
    $this->assertEquals($pos, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek($pos);
    $token = $this->object->findPreviousByType(T_CLASS, $pos, T_NAMESPACE);
    $this->assertType('DocBlox_Token', $token, 'Expected to find a T_CLASS in the dataset within '.$pos.' tokens before a T_NAMESPACE is encountered');
    $this->assertEquals($pos, $this->object->key(), 'Expected the key to have a different position');

    $this->object->seek($pos);
    $token = $this->object->findPreviousByType(T_CLASS, $pos, T_FUNCTION);
    $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within '.$pos.' tokens before a T_FUNCTION is encountered');
    $this->assertEquals($pos, $this->object->key(), 'Expected the key to be at the starting position');
  }

  public function testGetTokenIdsOfBracePair()
  {
    $this->object->seek(0);
    $this->object->gotoNextByType(T_CLASS, 0);
    $result = $this->object->getTokenIdsOfBracePair();

    $this->assertType('array', $result, 'Expected result to be an array');
    $this->assertArrayHasKey(0, $result, 'Expected result to have a start element');
    $this->assertArrayHasKey(1, $result, 'Expected result to have an end element');
    $this->assertEquals(33, $result[0], 'Expected the first brace to be at token id 33');
    $this->assertEquals(57, $result[1], 'Expected the closing brace to be at token id 57');
  }

  public function testGetTokenIdsOfParenthesisPair()
  {
    $this->object->seek(0);
    $this->object->gotoNextByType(T_FUNCTION, 0);
    $result = $this->object->getTokenIdsOfParenthesisPair();

    $this->assertType('array', $result, 'Expected result to be an array');
    $this->assertArrayHasKey(0, $result, 'Expected result to have a start element');
    $this->assertArrayHasKey(1, $result, 'Expected result to have an end element');
    $this->assertEquals(40, $result[0], 'Expected the first brace to be at token id 40');
    $this->assertEquals(41, $result[1], 'Expected the closing brace to be at token id 41');
  }
}

?>
