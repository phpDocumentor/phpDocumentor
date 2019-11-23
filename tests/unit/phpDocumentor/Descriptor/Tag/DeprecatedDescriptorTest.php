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

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the DeprecatedDescriptor class.
 */
class DeprecatedDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    const EXAMPLE_VERSION = '2.0';

    /** @var DeprecatedDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new DeprecatedDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\DeprecatedDescriptor::setVersion
     * @covers \phpDocumentor\Descriptor\Tag\DeprecatedDescriptor::getVersion
     */
    public function testSetAndGetVersion() : void
    {
        $this->assertEmpty($this->fixture->getVersion());

        $this->fixture->setVersion(self::EXAMPLE_VERSION);
        $result = $this->fixture->getVersion();

        $this->assertSame(self::EXAMPLE_VERSION, $result);
    }
}
