<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the VersionDescriptor class.
 */
class VersionDescriptorTest extends MockeryTestCase
{
    public const EXAMPLE_VERSION = '2.0';

    /** @var VersionDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new VersionDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\VersionDescriptor::setVersion
     * @covers \phpDocumentor\Descriptor\Tag\VersionDescriptor::getVersion
     */
    public function testSetAndGetVersion() : void
    {
        $this->assertEmpty($this->fixture->getVersion());

        $this->fixture->setVersion(self::EXAMPLE_VERSION);
        $result = $this->fixture->getVersion();

        $this->assertEquals(self::EXAMPLE_VERSION, $result);
    }
}
