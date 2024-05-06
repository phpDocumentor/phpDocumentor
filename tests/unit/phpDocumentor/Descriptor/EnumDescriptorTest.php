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
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the ClassDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\EnumDescriptor
 */
final class EnumDescriptorTest extends MockeryTestCase
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

        $mock = m::mock(Collection::class);

        $this->fixture->setInterfaces($mock);

        $this->assertSame($mock, $this->fixture->getInterfaces());
    }

    public function testSettingAndGettingMethods(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = m::mock(Collection::class);

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }
}
