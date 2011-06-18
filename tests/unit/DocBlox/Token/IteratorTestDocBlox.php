<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Tokens
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * TokenIterator mock class.
 *
 * @category   DocBlox
 * @package    Tokens
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_TokenIteratorMock extends DocBlox_Reflection_TokenIterator {
    public function gotoUpByType() {
        $this->gotoTokenByTypeInDirection('{', 'up');
    }

    public function getBrokenTokenIdsOfBracePair() {
        return $this->getTokenIdsBetweenPair('}', '{');
    }
}

/**
 * Test class for DocBlox_Reflection_TokenIterator.
 *
 * @category   DocBlox
 * @package    Tokens
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Token_IteratorTest extends PHPUnit_Framework_TestCase
{
    /** @var array[] tokens returned by token_get_all */
    protected $tokens = array();

    protected $fixture = <<<FIXTURE
<?php
namespace myBla;

class Bla
{
    public function testBla()
    {
    }
}
FIXTURE;

    /**
     * @var DocBlox_Reflection_TokenIterator
     */
    protected $object;

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp() {
        $this->tokens = token_get_all($this->fixture);
        $this->object = new DocBlox_Reflection_TokenIterator($this->tokens);

        $this->object->seek(0);
    }

    /**
     * Tests whether the token iterator is properly constructed and contains
     * only DocBlox_Reflection_Token objects.
     *
     * @return void
     */
    public function testConstruct() {
        $this->assertGreaterThan(
            0, count($this->object),
            'Expected DocBlox_Reflection_TokenIterator to contain more than 0 items'
        );
        $this->assertEquals(
            count($this->tokens),
            count($this->object),
            'Expected DocBlox_Reflection_TokenIterator to contain the same amount of '
            . 'items as the output of the tokenizer'
        );

        foreach ($this->object as $token)
        {
            if (!($token instanceof DocBlox_Reflection_Token)) {
                $this->fail('All tokens in the DocBlox_Reflection_TokenIterator are '
                . 'expected to be of type DocBlox_Reflection_Token, found: '
                . print_r($token, true));
            }
        }

        // test by inserting an array of Tokens ($this->object is
        // effectively an array of objects)
        $test_array = array(
            $this->object[0], $this->object[1], $this->object[2]
        );

        $other_object = new DocBlox_Reflection_TokenIterator($test_array);
        $this->assertGreaterThan(
            0, count($other_object),
            'Expected DocBlox_Reflection_TokenIterator to contain more than 0 items'
        );
        $this->assertEquals(
            count($test_array),
            count($other_object),
            'Expected DocBlox_Reflection_TokenIterator to contain the same amount of '
            . 'items as the given array of Tokens'
        );
    }

    public function testGotoTokenByTypeInDirection() {
        $tokens = token_get_all($this->fixture);
        $mock = new DocBlox_Reflection_TokenIteratorMock($tokens);

        $this->setExpectedException('InvalidArgumentException');
        // expect an exception because this stub tries to use a wrong direction
        $mock->gotoUpByType();
    }

    /**
     * Tests the gotoNextByType method
     */
    public function testGotoNextByType() {
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
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset');
        $this->assertNotEquals(0, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek(0);
        $token = $this->object->gotoNextByType(T_CLASS, 10);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens');
        $this->assertNotEquals(0, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek(0);
        $token = $this->object->gotoNextByType(T_CLASS, 10, T_REQUIRE);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens before a T_REQUIRE is encountered');
        $this->assertNotEquals(0, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek(0);
        $token = $this->object->gotoNextByType(T_CLASS, 10, T_NAMESPACE);
        $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within 40 tokens before a T_NAMESPACE is encountered');
        $this->assertEquals(0, $this->object->key(), 'Expected the key to be at the starting position');
    }

    /**
     * Tests the gotoNextByType method
     */
    public function testGotoPreviousByType()
    {
        $pos = 20;

        $this->object->seek($pos);
        $token = $this->object->gotoPreviousByType(T_CLASS, 0);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset');
        $this->assertNotEquals($pos, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek($pos);
        $token = $this->object->gotoPreviousByType(T_CLASS, $pos);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within ' . $pos . ' tokens');
        $this->assertNotEquals($pos, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek($pos);
        $token = $this->object->gotoPreviousByType(T_CLASS, $pos, T_NAMESPACE);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within ' . $pos . ' tokens before a T_NAMESPACE is encountered');
        $this->assertNotEquals($pos, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek($pos);
        $token = $this->object->gotoPreviousByType(T_CLASS, $pos, T_FUNCTION);
        $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within ' . $pos . ' tokens before a T_FUNCTION is encountered');
        $this->assertEquals($pos, $this->object->key(), 'Expected the key to be at the starting position');
    }

    public function testFindNextByType() {
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
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset');
        $this->assertEquals(0, $this->object->key(), 'Expected the key to equal the starting position');

        $this->object->seek(0);
        $token = $this->object->findNextByType(T_CLASS, 40);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens');
        $this->assertEquals(0, $this->object->key(), 'Expected the key to equal the starting position');

        $this->object->seek(0);
        $token = $this->object->findNextByType(T_CLASS, 40, T_REQUIRE);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within 40 tokens before a T_REQUIRE is encountered');
        $this->assertEquals(0, $this->object->key(), 'Expected the key to equal the starting position');

        $this->object->seek(0);
        $token = $this->object->findNextByType(T_CLASS, 40, T_NAMESPACE);
        $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within 40 tokens before a T_NAMESPACE is encountered');
        $this->assertEquals(0, $this->object->key(), 'Expected the key to be at the starting position');
    }

    public function testFindPreviousByType() {
        $pos = 20;

        $this->object->seek($pos);
        $token = $this->object->findPreviousByType(T_CLASS, 0);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset');
        $this->assertEquals($pos, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek($pos);
        $token = $this->object->findPreviousByType(T_CLASS, $pos);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within ' . $pos . ' tokens');
        $this->assertEquals($pos, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek($pos);
        $token = $this->object->findPreviousByType(T_CLASS, $pos, T_NAMESPACE);
        $this->assertInstanceOf('DocBlox_Reflection_Token', $token, 'Expected to find a T_CLASS in the dataset within ' . $pos . ' tokens before a T_NAMESPACE is encountered');
        $this->assertEquals($pos, $this->object->key(), 'Expected the key to have a different position');

        $this->object->seek($pos);
        $token = $this->object->findPreviousByType(T_CLASS, $pos, T_FUNCTION);
        $this->assertFalse($token, 'Expected to fail finding a T_CLASS in the dataset within ' . $pos . ' tokens before a T_FUNCTION is encountered');
        $this->assertEquals($pos, $this->object->key(), 'Expected the key to be at the starting position');
    }

    public function testGetBrokenTokenIdsOfBracePair() {
        $tokens = token_get_all(file_get_contents(dirname(__FILE__) . '/../../../data/TokenIteratorTestFixture.php'));
        $mock = new DocBlox_Reflection_TokenIteratorMock($tokens);

        // because we have switched the { and } in the stub method it should immediately find a closing brace and thus
        // return null,null
        $mock->seek(0);
        $mock->gotoNextByType(T_CLASS, 0);
        $this->assertEquals(array(null, null), $mock->getBrokenTokenIdsOfBracePair());
    }

    public function testGetTokenIdsOfBracePair()
    {
        $this->object->seek(0);
        $this->object->gotoNextByType(T_CLASS, 0);
        $result = $this->object->getTokenIdsOfBracePair();

        $this->assertInternalType('array', $result, 'Expected result to be an array');
        $this->assertArrayHasKey(0, $result, 'Expected result to have a start element');
        $this->assertArrayHasKey(1, $result, 'Expected result to have an end element');
        $this->assertEquals(10, $result[0], 'Expected the first brace to be at token id 10');
        $this->assertEquals(24, $result[1], 'Expected the closing brace to be at token id 24');
    }

    public function testGetTokenIdsOfParenthesisPair()
    {
        $this->object->seek(0);
        $this->object->gotoNextByType(T_FUNCTION, 0);
        $result = $this->object->getTokenIdsOfParenthesisPair();

        $this->assertInternalType('array', $result, 'Expected result to be an array');
        $this->assertArrayHasKey(0, $result, 'Expected result to have a start element');
        $this->assertArrayHasKey(1, $result, 'Expected result to have an end element');
        $this->assertEquals(17, $result[0], 'Expected the first brace to be at token id 17');
        $this->assertEquals(18, $result[1], 'Expected the closing brace to be at token id 18');
    }
}