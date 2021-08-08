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

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the UsesDescriptor class.
 */
class UsesDescriptorTest extends TestCase
{
    public const EXAMPLE_REFERENCE = '\Reference';

    /** @var UsesDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new UsesDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\UsesDescriptor::setReference
     * @covers \phpDocumentor\Descriptor\Tag\UsesDescriptor::getReference
     */
    public function testSetAndGetReference(): void
    {
        $this->assertNull($this->fixture->getReference());

        $this->fixture->setReference(new Fqsen(self::EXAMPLE_REFERENCE));
        $result = $this->fixture->getReference();

        $this->assertSame(self::EXAMPLE_REFERENCE, (string) $result);
    }
}
