<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the LinkDescriptor class.
 */
final class LinkDescriptorTest extends MockeryTestCase
{
    public const EXAMPLE_LINK = 'https://phpdoc.org';

    /** @var LinkDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new LinkDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\LinkDescriptor::setLink
     * @covers \phpDocumentor\Descriptor\Tag\LinkDescriptor::getLink
     */
    public function testSetAndGetLink() : void
    {
        $this->assertEmpty($this->fixture->getLink());

        $this->fixture->setLink(self::EXAMPLE_LINK);
        $result = $this->fixture->getLink();

        $this->assertSame(self::EXAMPLE_LINK, $result);
    }
}
