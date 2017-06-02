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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\ClassReflector\PropertyReflector;
use Mockery as m;

class PropertyAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var PropertyAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->builderMock->shouldReceive('buildDescriptor')->andReturn(null);

        $this->fixture = new PropertyAssembler();
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler::create
     */
    public function testCreatePropertyDescriptorFromReflector()
    {
        // Arrange
        $namespace    = 'Namespace';
        $propertyName   = 'property';

        $propertyReflectorMock = $this->givenAPropertyReflector(
            $namespace,
            $propertyName,
            $this->givenADocBlockObject(true)
        );

        // Act
        $descriptor = $this->fixture->create($propertyReflectorMock);

        // Assert
        $expectedFqsen = $namespace . '\\$' . $propertyName;
        $this->assertSame($expectedFqsen, $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($propertyName, $descriptor->getName());
        $this->assertSame('\\' . $namespace, $descriptor->getNamespace());
        $this->assertSame('protected', $descriptor->getVisibility());
        $this->assertSame(false, $descriptor->isStatic());
    }

    /**
     * Creates a sample property reflector for the tests with the given data.
     *
     * @param string                             $namespace
     * @param string                             $propertyName
     * @param DocBlock|m\MockInterface           $docBlockMock
     *
     * @return PropertyReflector|m\MockInterface
     */
    protected function givenAPropertyReflector($namespace, $propertyName, $docBlockMock = null)
    {
        $propertyReflectorMock = m::mock('phpDocumentor\Reflection\PropertyReflector');
        $propertyReflectorMock->shouldReceive('getName')->andReturn($namespace . '\\$' . $propertyName);
        $propertyReflectorMock->shouldReceive('getShortName')->andReturn($propertyName);
        $propertyReflectorMock->shouldReceive('getNamespace')->andReturn($namespace);
        $propertyReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);
        $propertyReflectorMock->shouldReceive('getLinenumber')->andReturn(128);
        $propertyReflectorMock->shouldReceive('getVisibility')->andReturn('protected');
        $propertyReflectorMock->shouldReceive('getDefault')->andReturn(null);
        $propertyReflectorMock->shouldReceive('isStatic')->andReturn(false);

        return $propertyReflectorMock;
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     *
     * @return DocBlock|m\MockInterface
     */
    protected function givenADocBlockObject($withTags)
    {
        $docBlockDescription = new DocBlock\Description('This is an example description');

        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        if ($withTags) {
            $docBlockMock->shouldReceive('getTagsByName')->andReturnUsing(function ($param) {
                $tag = m::mock('phpDocumentor\Reflection\DocBlock\Tag');

                $tag->shouldReceive('isVariadic')->once()->andReturn(true);
                $tag->shouldReceive('getVariableName')->andReturn('variableName');
                $tag->shouldReceive('getTypes')->andReturn(array());
                $tag->shouldReceive('getDescription');

                return array($tag);
            });
        } else {
            $docBlockMock->shouldReceive('getTagsByName')->andReturn(array());
        }

        return $docBlockMock;
    }
}
