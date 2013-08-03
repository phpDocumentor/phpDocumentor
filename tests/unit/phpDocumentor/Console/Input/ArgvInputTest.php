<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Input;

use Mockery as m;

/**
 * Tests the functionality for the InputStream class for phpDocumentor.
 */
class ArgvInputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers       phpDocumentor\Console\Input\ArgvInput::__construct
     * @dataProvider provideArgvArrays
     */
    public function testPrependsCommandName($argvArray, $expected)
    {
        $input = new ArgvInput($argvArray);
        $this->assertAttributeEquals($expected, 'tokens', $input);
    }

    /**
     * @covers phpDocumentor\Console\Input\ArgvInput::__construct
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
