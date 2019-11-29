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
 * Tests the functionality for the SeeDescriptor class.
 */
class SeeDescriptorTest extends MockeryTestCase
{
    public const EXAMPLE_REFERENCE = 'reference';

    /** @var SeeDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new SeeDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\SeeDescriptor::setReference
     * @covers \phpDocumentor\Descriptor\Tag\SeeDescriptor::getReference
     */
    public function testSetAndGetReference() : void
    {
        $this->assertEmpty($this->fixture->getReference());

        $this->fixture->setReference(self::EXAMPLE_REFERENCE);
        $result = $this->fixture->getReference();

        $this->assertEquals(self::EXAMPLE_REFERENCE, $result);
    }
}
