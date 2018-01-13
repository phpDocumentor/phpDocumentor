<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Input;

/**
 * Tests the functionality for the InputStream class for phpDocumentor.
 */
class ArgvInputTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
        $_SERVER['argv'] = ['foo', 'bar', 'pizza'];

        $input = new ArgvInput(null);

        $this->assertAttributeEquals(['bar', 'pizza'], 'tokens', $input);
    }

    /**
     * Provides a series of Argv arrays to use as test scenarios for the testPrependsCommandName test.
     *
     * @return array[][]
     */
    public function provideArgvArrays()
    {
        return [
            [['bla'], ['project:run']],
            [['bla', 'project:parse'], ['project:parse']],
            [['bla', '-f', 'blabla'], ['project:run', '-f', 'blabla']],
        ];
    }
}
