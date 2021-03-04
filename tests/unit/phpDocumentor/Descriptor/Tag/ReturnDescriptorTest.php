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

use phpDocumentor\Reflection\Types\Array_;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the ReturnDescriptor class.
 */
class ReturnDescriptorTest extends TestCase
{
    /** @var ReturnDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new ReturnDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::setTypes
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::setType
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::getTypes
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::getType
     */
    public function testSetAndGetType() : void
    {
        $expected = new Array_();
        $this->assertNull($this->fixture->getType());

        $this->fixture->setType($expected);
        $result = $this->fixture->getType();

        $this->assertEquals($expected, $result);
    }
}
