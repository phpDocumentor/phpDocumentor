<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Trait_;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @uses \phpDocumentor\Descriptor\DescriptorAbstract
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler
 * @covers ::<private>
 */
final class TraitAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @uses \phpDocumentor\Descriptor\Collection
     * @uses \phpDocumentor\Descriptor\MethodDescriptor
     * @uses \phpDocumentor\Descriptor\TraitDescriptor
     *
     * @covers ::create
     */
    public function testAssembleTraitWithMethod(): void
    {
        $method = new MethodDescriptor();
        $method->setName('method');
        $builder = $this->prophesize(ApiSetDescriptorBuilder::class);
        $builder->buildDescriptor(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($method);

        $traitFqsen = new Fqsen('\My\Space\MyTrait');
        $trait = new Trait_($traitFqsen);
        $trait->addMethod(new Method(new Fqsen('\My\Space\MyTrait::method()')));
        $assembler = new TraitAssembler();
        $assembler->setBuilder($builder->reveal());

        $result = $assembler->create($trait);

        static::assertEquals('\My\Space', $result->getNamespace());
        static::assertSame($traitFqsen, $result->getFullyQualifiedStructuralElementName());
        static::assertEquals('MyTrait', $result->getName());
        static::assertInstanceOf(
            MethodDescriptor::class,
            $result->getMethods()->fetch('method', false)
        );
    }
}
