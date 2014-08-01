<?php
/**
* phpDocumentor
*
* PHP Version 5.3
*
* @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link http://phpdoc.org
*/

namespace phpDocumentor\Translator;

/**
 * Tests for phpDocumentor\Translator\ConfigurationTest
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Configuration $fixture */
    protected $fixture = null;

    /**
     * Setup test fixture and mocks used in this TestCase
     */
    protected function setUp()
    {
        $this->fixture = new Configuration();
    }

    /**
     * @covers phpDocumentor\Translator\Configuration::getLocale
     */
    public function testGetLocale()
    {
        $this->assertSame('en', $this->fixture->getLocale());
    }
}
