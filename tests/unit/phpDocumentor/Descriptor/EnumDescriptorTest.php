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

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the ClassDescriptor class.
 */
#[CoversClass(EnumDescriptor::class)]
final class EnumDescriptorTest extends TestCase
{
    /** @var EnumDescriptor $fixture */
    private $fixture;
    use MagicMethodContainerTests;
    use TraitUsageTests;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new EnumDescriptor();
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\My\Enum'));
    }

    public function testSettingAndGettingInterfaces(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getInterfaces());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setInterfaces($mock);

        $this->assertSame($mock, $this->fixture->getInterfaces());
    }

    public function testSettingAndGettingMethods(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }
}
