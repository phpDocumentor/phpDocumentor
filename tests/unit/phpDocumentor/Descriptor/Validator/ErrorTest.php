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
    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     */
    public function testIfCanBeInstantiated()
    {
        $error = new Error('severity', 'code', 0, array());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Validator\Error', $error);
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getSeverity
     */
    public function testIfSeverityCanBeReturned()
    {
        $severity = 'severity';
        $error = new Error($severity, 'code', 0, array());
        $this->assertSame($severity, $error->getSeverity());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getCode
     */
    public function testIfCodeCanBeReturned()
    {
        $code = 'foo';
        $error = new Error('severity', $code, 0, array());
        $this->assertSame($code, $error->getCode());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getLine
     */
    public function testIfLineCanBeReturned()
    {
        $line = 1337;
        $error = new Error('severity', 'code', $line, array());
        $this->assertSame($line, $error->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Error::__construct
     * @covers phpDocumentor\Descriptor\Validator\Error::getContext
     */
    public function testIfContextCanBeReturned()
    {
        $context = array();
        $error = new Error('severity', 'code', 0, $context);
        $this->assertSame($context, $error->getContext());
    }
}