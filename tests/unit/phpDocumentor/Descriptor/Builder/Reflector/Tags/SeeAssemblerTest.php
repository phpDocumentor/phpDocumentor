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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\Fqsen;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler
 * @covers ::<private>
 */
class SeeAssemblerTest extends MockeryTestCase
{
    /** @var SeeAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new SeeAssembler();
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers ::create
     */
    public function testCreateSeeDescriptorFromSeeTagWhenReferenceIsRelativeClassnameNotInNamespaceAliasses() : void
    {
        // Arrange
        $name = 'see';
        $description = 'a see tag';
        $reference = '\ReferenceClass';

        $seeTagMock = $this->givenASeeTag(new DocBlock\Tags\Reference\Fqsen(new Fqsen($reference)), $description);

        // Act
        $descriptor = $this->fixture->create($seeTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, (string) $descriptor->getDescription());
        $this->assertSame($reference, (string) $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    /**
     * @covers ::create
     * @dataProvider provideReferences
     */
    public function testCreateSeeDescriptorFromSeeTagWhenReferenceIsUrl($reference) : void
    {
        // Arrange
        $name = 'see';
        $description = 'a see tag';

        $seeTagMock = $this->givenASeeTag($reference, $description);

        // Act
        $descriptor = $this->fixture->create($seeTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, (string) $descriptor->getDescription());
        $this->assertSame($reference, $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    protected function givenASeeTag($reference, $description) : See
    {
        return new DocBlock\Tags\See(
            $reference,
            new DocBlock\Description($description)
        );
    }

    protected function givenADocBlock($context) : DocBlock
    {
        return new DocBlock('', null, [], $context);
    }

    public function provideReferences() : array
    {
        return [
            [new DocBlock\Tags\Reference\Url('http://phpdoc.org')],
            [new DocBlock\Tags\Reference\Url('https://phpdoc.org')],
            [new DocBlock\Tags\Reference\Url('ftp://phpdoc.org')],
            [new DocBlock\Tags\Reference\Fqsen(new Fqsen('\My\Namespace\Class'))],
        ];
    }
}
