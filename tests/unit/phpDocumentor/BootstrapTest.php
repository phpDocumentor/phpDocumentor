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

namespace phpDocumentor;

/**
 * Test class for \phpDocumentor\Bootstrap.
 *
 * @covers phpDocumentor\Bootstrap
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Bootstrap::createInstance
     */
    public function testCreatingAnInstanceUsingStaticFactoryMethod()
    {
        $this->assertInstanceOf('phpDocumentor\Bootstrap', Bootstrap::createInstance());
    }

    /**
     * @covers phpDocumentor\Bootstrap::initialize
     */
    public function testInitializingTheApplication()
    {
        $bootstrap = Bootstrap::createInstance();
        $this->assertInstanceOf('phpDocumentor\Application', $bootstrap->initialize());
    }
}
