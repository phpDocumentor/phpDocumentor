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
namespace phpDocumentor\Transformer\Template;

use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Parameter class.
 * @coversDefaultClass \phpDocumentor\Transformer\Template\Parameter
 */
class ParameterTest extends TestCase
{
    /** @var Parameter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new Parameter();
    }

    /**
     * @covers ::getKey
     * @covers ::setKey
     */
    public function testSetAndGetKey() : void
    {
        $this->assertEmpty($this->fixture->getKey());

        $this->fixture->setKey('key');

        $this->assertSame('key', $this->fixture->getKey());
    }

    /**
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testSetAndGetValue() : void
    {
        $this->assertEmpty($this->fixture->getValue());

        $this->fixture->setValue('value');

        $this->assertSame('value', $this->fixture->getValue());
    }
}
