<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler
 * @covers ::<private>
 */
class SeeAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var SeeAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new SeeAssembler();
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers ::create
     */
    public function testCreateSeeDescriptorFromSeeTagWhenReferenceIsRelativeClassnameNotInNamespaceAliasses()
    {
        // Arrange
        $name = 'see';
        $description = 'a see tag';
        $reference = 'ReferenceClass';
        $context = $this->givenAContext([$reference => '\My\Namespace\Alias\AnotherClass']);
        $docBlock = $this->givenADocBlock($context);

        $seeTagMock = $this->givenASeeTag($name, $description, $reference, $docBlock);

        // Act
        $descriptor = $this->fixture->create($seeTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, $descriptor->getDescription());
        $this->assertSame('@context::' . $reference, $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    /**
     * @covers ::create
     */
    public function testCreateSeeDescriptorFromSeeTagWhenReferenceIsRelativeClassnameInNamespaceAliases()
    {
        // Arrange
        $name = 'see';
        $description = 'a see tag';
        $reference = 'ReferenceClass';
        $context = $this->givenAContext([$reference => '\My\Namespace\Alias\ReferenceClass']);
        $docBlock = $this->givenADocBlock($context);

        $seeTagMock = $this->givenASeeTag($name, $description, $reference, $docBlock);

        // Act
        $descriptor = $this->fixture->create($seeTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, $descriptor->getDescription());
        $this->assertSame('\\My\\Namespace\Alias\\' . $reference, $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    /**
     * @covers ::create
     * @dataProvider provideReferences
     */
    public function testCreateSeeDescriptorFromSeeTagWhenReferenceIsUrl($reference)
    {
        // Arrange
        $name = 'see';
        $description = 'a see tag';
        $context = $this->givenAContext([]);
        $docBlock = $this->givenADocBlock($context);

        $seeTagMock = $this->givenASeeTag($name, $description, $reference, $docBlock);

        // Act
        $descriptor = $this->fixture->create($seeTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, $descriptor->getDescription());
        $this->assertSame($reference, $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    /**
     * @covers ::create
     */
    public function testCreateSeeDescriptorFromSeeTagWhenReferenceHasMultipleParts()
    {
        // Arrange
        $name = 'see';
        $description = 'a see tag';
        $reference = 'ReferenceClass::$property';
        $context = $this->givenAContext(['ReferenceClass' => '\My\Namespace\Alias\ReferenceClass']);
        $docBlock = $this->givenADocBlock($context);

        $seeTagMock = $this->givenASeeTag($name, $description, $reference, $docBlock);

        // Act
        $descriptor = $this->fixture->create($seeTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, $descriptor->getDescription());
        $this->assertSame('\\My\\Namespace\Alias\\' . $reference, $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    protected function givenASeeTag($name, $description, $reference, $docBlock)
    {
        $seeTagMock = m::mock('phpDocumentor\Reflection\DocBlock\Tag\SeeTag');
        $seeTagMock->shouldReceive('getName')->andReturn($name);
        $seeTagMock->shouldReceive('getDescription')->andReturn($description);
        $seeTagMock->shouldReceive('getReference')->andReturn($reference);
        $seeTagMock->shouldReceive('getDocBlock')->andReturn($docBlock);

        return $seeTagMock;
    }

    protected function givenADocBlock($context)
    {
        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getContext')->andReturn($context);

        return $docBlockMock;
    }

    protected function givenAContext($aliases)
    {
        $context = m::mock('phpDocumentor\Reflection\DocBlock\Context');
        $context->shouldReceive('getNamespace')->andReturn('\My\Namespace');
        $context->shouldReceive('getNamespaceAliases')->andReturn($aliases);

        return $context;
    }

    public function provideReferences()
    {
        return [
            ['http://phpdoc.org'],
            ['https://phpdoc.org'],
            ['ftp://phpdoc.org'],
            ['$this'],
            ['self'],
            ['\My\Namespace\Class']
        ];
    }
}
