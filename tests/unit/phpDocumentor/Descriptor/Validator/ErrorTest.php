<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Validator;

/**
 * Tests the functionality for the Error class.
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_SEVERITY = 'severity';
    const EXAMPLE_CODE     = 'code';
    const EXAMPLE_LINE     = 0;

    /** @var Error */
    private $fixture;

    /**
     * Creates a fixture for this test.
     */
    protected function setUp()
    {
        $this->fixture = new Error(self::EXAMPLE_SEVERITY, self::EXAMPLE_CODE, self::EXAMPLE_LINE, array('context'));
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getSeverity
     */
    public function testIfSeverityCanBeReturned()
    {
        $this->assertSame(self::EXAMPLE_SEVERITY, $this->fixture->getSeverity());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getCode
     */
    public function testIfCodeCanBeReturned()
    {
        $this->assertSame(self::EXAMPLE_CODE, $this->fixture->getCode());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getLine
     */
    public function testIfLineCanBeReturned()
    {
        $this->assertSame(self::EXAMPLE_LINE, $this->fixture->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getContext
     */
    public function testIfContextCanBeReturned()
    {
        $this->assertSame(array('context'), $this->fixture->getContext());
    }
}
