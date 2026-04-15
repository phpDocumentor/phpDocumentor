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

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ThrowsAssemblerTest extends TestCase
{
    use ProphecyTrait;

    private ThrowsAssembler $fixture;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new ThrowsAssembler();
        $this->fixture->setBuilder($this->builder->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler::buildDescriptor
     */
    public function testCreatingThrowsDescriptorFromReflector(): void
    {
        $types = new Object_(new Fqsen('\InvalidAgumentException'));
        $reflector = new Throws(
            $types,
            new Description('This is a description'),
        );

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('throws', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertSame($types, $descriptor->getType());
    }
}
