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
 * Tests the functionality for the DeprecatedDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Tag\DeprecatedDescriptor
 */
final class DeprecatedDescriptorTest extends MockeryTestCase
{
    public const EXAMPLE_VERSION = '2.0';

    /** @var DeprecatedDescriptor $fixture */
    private $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->markTestIncomplete('Review this whole testcase; it is too complicated to change');
        $this->fixture = new DeprecatedDescriptor('name');
    }

    /**
     * @covers ::setVersion
     * @covers ::getVersion
     */
    public function testSetAndGetVersion() : void
    {
        $this->assertEmpty($this->fixture->getVersion());

        $this->fixture->setVersion(self::EXAMPLE_VERSION);
        $result = $this->fixture->getVersion();

        $this->assertSame(self::EXAMPLE_VERSION, $result);
    }
}
