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

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the VersionDescriptor class.
 */
class VersionDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_VERSION = '2.0';

    /** @var VersionDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new VersionDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\VersionDescriptor::setVersion
     * @covers phpDocumentor\Descriptor\Tag\VersionDescriptor::getVersion
     */
    public function testSetAndGetVersion()
    {
        $this->assertEmpty($this->fixture->getVersion());

        $this->fixture->setVersion(self::EXAMPLE_VERSION);
        $result = $this->fixture->getVersion();

        $this->assertEquals(self::EXAMPLE_VERSION, $result);
    }
}
