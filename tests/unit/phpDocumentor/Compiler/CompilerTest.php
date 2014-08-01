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

namespace phpDocumentor\Compiler;

/**
 * Tests the functionality for the Compiler.
 */
class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->fixture = new Compiler();
    }

    /**
     * @covers phpDocumentor\Compiler\Compiler::insert
     */
    public function testDefaultPassHasDefaultPriority()
    {
        $this->fixture->insert('test');
        $this->fixture->setExtractFlags(Compiler::EXTR_PRIORITY);

        $this->assertEquals(Compiler::PRIORITY_DEFAULT, $this->fixture->extract());
    }
}
