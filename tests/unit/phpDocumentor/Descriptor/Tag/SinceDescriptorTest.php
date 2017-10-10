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
 * Tests the functionality for the SinceDescriptor class.
 */
class SinceDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    const EXAMPLE_VERSION = 'version';

    /** @var SinceDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new SinceDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\SinceDescriptor::setVersion
     * @covers phpDocumentor\Descriptor\Tag\SinceDescriptor::getVersion
     */
    public function testSetAndGetVersion()
    {
        $this->assertEmpty($this->fixture->getVersion());

        $this->fixture->setVersion(self::EXAMPLE_VERSION);
        $result = $this->fixture->getVersion();

        $this->assertEquals(self::EXAMPLE_VERSION, $result);
    }
}
