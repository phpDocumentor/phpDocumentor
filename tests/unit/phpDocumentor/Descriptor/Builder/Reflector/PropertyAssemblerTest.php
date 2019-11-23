<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\String_;

class PropertyAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
        $namespace = 'Namespace';
        $propertyName = 'property';

        $propertyReflectorMock = $this->givenAPropertyReflector(
            $namespace,
            $propertyName,
            $this->givenADocBlockObject(true)
        );

        // Act
        $descriptor = $this->fixture->create($propertyReflectorMock);

        // Assert
        $expectedFqsen = '\\' . $namespace . '::$' . $propertyName;
        $this->assertSame($expectedFqsen, (string) $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($propertyName, $descriptor->getName());
        $this->assertSame('\\' . $namespace, $descriptor->getNamespace());
        $this->assertSame('protected', (string) $descriptor->getVisibility());
        $this->assertFalse($descriptor->isStatic());
    }

    /**
     * Creates a sample property reflector for the tests with the given data.
     *
     * @param string $namespace
     * @param string $propertyName
     * @param DocBlock $docBlockMock
     */
    protected function givenAPropertyReflector($namespace, $propertyName, $docBlockMock = null): Property
    {
        return new Property(
            new Fqsen('\\' . $namespace . '::$' . $propertyName),
            new Visibility(Visibility::PROTECTED_),
            $docBlockMock
        );
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     */
    protected function givenADocBlockObject($withTags): DocBlock
    {
        $docBlockDescription = new Description('This is an example description');

        $tags = [];

        if ($withTags) {
            $tags[] = new DocBlock\Tags\Var_('variableName', new String_(), new Description('Var description'));
        }

        return new DocBlock('This is a example description', $docBlockDescription, $tags);
    }
}
