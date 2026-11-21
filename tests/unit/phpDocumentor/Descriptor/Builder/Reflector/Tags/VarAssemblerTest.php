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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

#[CoversClass(VarAssembler::class)]
class VarAssemblerTest extends TestCase
{
    use ProphecyTrait;

    private VarAssembler $fixture;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new VarAssembler();
        $this->fixture->setBuilder($this->builder->reveal());
    }

    public function testCreatingVarDescriptorFromReflector(): void
    {
        $reflector = new Var_('$myParameter', new String_(), new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('var', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertSame('$myParameter', $descriptor->getVariableName());
        $this->assertEquals(new String_(), $descriptor->getType());
    }
}
