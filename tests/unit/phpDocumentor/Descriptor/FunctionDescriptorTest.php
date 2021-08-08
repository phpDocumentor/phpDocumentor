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

namespace phpDocumentor\Descriptor;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\Types\String_;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\FunctionDescriptor
 */
final class FunctionDescriptorTest extends MockeryTestCase
{
    /** @var FunctionDescriptor $fixture */
    private $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new FunctionDescriptor();
    }

    /**
     * @covers ::__construct
     */
    public function testInitializesWithEmptyCollection(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());
    }

    /**
     * @covers ::setArguments
     * @covers ::getArguments
     */
    public function testSettingAndGettingArguments(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());

        $mockInstance = m::mock(Collection::class);
        $mock = &$mockInstance;

        $this->fixture->setArguments($mock);

        $this->assertSame($mockInstance, $this->fixture->getArguments());
    }

    /**
     * @covers ::getResponse
     * @covers ::setReturnType
     */
    public function testSettingAndGettingReturnType(): void
    {
        $stringType = new String_();
        $this->fixture->setReturnType($stringType);

        $this->assertSame('return', $this->fixture->getResponse()->getName());
        $this->assertSame($stringType, $this->fixture->getResponse()->getType());
    }
}
