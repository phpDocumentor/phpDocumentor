<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Cli\Input;

/**
 * Tests the functionality for the InputStream class for phpDocumentor.
 * @coversDefaultClass phpDocumentor\Application\Cli\Input\ArgvInput
 */
class ArgvInputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers       ::__construct
     * @dataProvider provideArgvArrays
     */
    public function testPrependsCommandName($argvArray, $expected)
    {
        $input = new ArgvInput($argvArray);
        $this->assertAttributeEquals($expected, 'tokens', $input);
    }

    /**
     * @covers ::__construct
     */
    public function testIfServerArgvIsArray()
    {
        $_SERVER['argv'] = array('foo', 'bar', 'pizza');

        $input = new ArgvInput(null);

        $this->assertAttributeEquals(array('bar', 'pizza'), 'tokens', $input);
    }

    /**
     * Provides a series of Argv arrays to use as test scenarios for the testPrependsCommandName test.
     *
     * @return array[][]
     */
    public function provideArgvArrays()
    {
        return array(
            array(array('bla'), array('project:run')),
            array(array('bla', 'project:parse'), array('project:parse')),
            array(array('bla', '-f', 'blabla'), array('project:run', '-f', 'blabla')),
        );
    }
}
