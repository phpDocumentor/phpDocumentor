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

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the Parameter class.
 */
class ParameterTest extends MockeryTestCase
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
     * @covers \phpDocumentor\Transformer\Template\Parameter::getKey
     * @covers \phpDocumentor\Transformer\Template\Parameter::setKey
     */
    public function testSetAndGetKey() : void
    {
        $this->assertNull($this->fixture->getKey());

        $this->fixture->setKey('key');

        $this->assertSame('key', $this->fixture->getKey());
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\Parameter::getValue
     * @covers \phpDocumentor\Transformer\Template\Parameter::setValue
     */
    public function testSetAndGetValue() : void
    {
        $this->assertNull($this->fixture->getValue());

        $this->fixture->setValue('value');

        $this->assertSame('value', $this->fixture->getValue());
    }
}
