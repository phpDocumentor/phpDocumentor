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

use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(FunctionDescriptor::class)]
final class FunctionDescriptorTest extends TestCase
{
    use ProphecyTrait;

    private FunctionDescriptor $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new FunctionDescriptor();
    }

    public function testInitializesWithEmptyCollection(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());
    }

    public function testSettingAndGettingArguments(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());

        $mockInstance = $this->prophesize(Collection::class)->reveal();
        $mock = $mockInstance;

        $this->fixture->setArguments($mock);

        $this->assertSame($mockInstance, $this->fixture->getArguments());
    }

    public function testSettingAndGettingReturnType(): void
    {
        $stringType = new String_();
        $this->fixture->setReturnType($stringType);

        $this->assertSame('return', $this->fixture->getResponse()->getName());
        $this->assertSame($stringType, $this->fixture->getResponse()->getType());
    }
}
