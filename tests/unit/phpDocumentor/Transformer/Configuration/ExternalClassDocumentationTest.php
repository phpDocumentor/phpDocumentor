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

namespace phpDocumentor\Transformer\Configuration;

class ExternalClassDocumentationTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_PREFIX       = 'prefix';
    const EXAMPLE_URI_TEMPLATE = 'uriTemplate';

    /** @var ExternalClassDocumentation */
    private $fixture;
   
    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new ExternalClassDocumentation(self::EXAMPLE_PREFIX, self::EXAMPLE_URI_TEMPLATE);
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\ExternalClassDocumentation::getPrefix
     */
    public function testIfPrefixCanBeRetrieved()
    {
        $this->assertSame(self::EXAMPLE_PREFIX, $this->fixture->getPrefix());
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\ExternalClassDocumentation::getUri
     */
    public function testIfUriCanBeRetrieved()
    {
        $this->assertSame(self::EXAMPLE_URI_TEMPLATE, $this->fixture->getUri());
    }
}
