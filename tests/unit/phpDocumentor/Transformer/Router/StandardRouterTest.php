<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\Fqsen as RealFqsen;

class StandardRouterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var StandardRouter */
    private $fixture;

    /** @var Collection */
    private $elementCollection;

    /**
     * Sets up the fixture.
     */
    protected function setUp()
    {
        $this->elementCollection = new Collection();

        $builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $builder
            ->shouldReceive('getProjectDescriptor->getIndexes->get')
            ->with('elements')
            ->andReturn($this->elementCollection);

        $this->fixture = new StandardRouter($builder);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\StandardRouter::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::__construct
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::match
     * @dataProvider provideDescriptorNames
     */
    public function testIfARouteForAFileCanBeGenerated($descriptorName, $generatorName = null)
    {
        // Arrange
        $generatorName = $generatorName ?: $descriptorName;
        $file = m::mock('phpDocumentor\\Descriptor\\' . $descriptorName);

        // Act
        $rule = $this->fixture->match($file);

        // Assert
        $this->assertInstanceOf('phpDocumentor\\Transformer\\Router\\Rule', $rule);
        $this->assertAttributeInstanceOf(
            '\phpDocumentor\\Transformer\\Router\\UrlGenerator\\Standard\\' . $generatorName,
            'generator',
            $rule
        );
    }

    /**
     * @covers phpDocumentor\Transformer\Router\StandardRouter::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::__construct
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::match
     */
    public function testIfARouteForAFqsenFileCanBeGenerated()
    {
        // Arrange
        $fqsen = new RealFqsen('\Fqsen');
        $file = new Fqsen($fqsen);

        // Act
        $rule = $this->fixture->match($file);

        // Assert
        $this->assertInstanceOf('phpDocumentor\\Transformer\\Router\\Rule', $rule);
        $this->assertAttributeInstanceOf(
            '\phpDocumentor\\Transformer\\Router\\UrlGenerator\\Standard\\FqsenDescriptor',
            'generator',
            $rule
        );
    }

    /**
     * @covers phpDocumentor\Transformer\Router\StandardRouter::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::__construct
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::match
     */
    public function testIfARouteForAUrlCanBeGenerated()
    {
        // Arrange
        $file = new Url('http://www.phpdoc.org');

        // Act
        $rule = $this->fixture->match($file);
        $result = $rule->generate($file);

        // Assert
        $this->assertInstanceOf('phpDocumentor\\Transformer\\Router\\Rule', $rule);
        $this->assertSame('http://www.phpdoc.org', $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\StandardRouter::configure
     * @covers phpDocumentor\Transformer\Router\StandardRouter::__construct
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::__construct
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::configure
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::match
     */
    public function testIfARouteForAFqsenCanBeGenerated()
    {
        // Arrange
        $fqsen = '\My\ClassName::myMethod()';
        $this->elementCollection[$fqsen] = m::mock('phpDocumentor\Descriptor\MethodDescriptor');

        // Act
        $rule = $this->fixture->match($fqsen);

        // Assert
        $this->assertInstanceOf('phpDocumentor\\Transformer\\Router\\Rule', $rule);
        $this->assertAttributeInstanceOf(
            '\phpDocumentor\\Transformer\\Router\\UrlGenerator\\Standard\\MethodDescriptor',
            'generator',
            $rule
        );
    }

    /**
     * @covers phpDocumentor\Transformer\Router\RouterAbstract::match
     */
    public function testGeneratingRouteForUnknownNodeReturnsFalse()
    {
        $this->assertFalse($this->fixture->match('Unknown')->generate('Unknown'));
    }

    /**
     * Returns the names of descriptors and generators supported by the StandardRouter.
     *
     * @return string[][]
     */
    public function provideDescriptorNames()
    {
        return [
            ['FileDescriptor'],
            ['NamespaceDescriptor'],
            ['PackageDescriptor'],
            ['ClassDescriptor'],
            ['InterfaceDescriptor', 'ClassDescriptor'],
            ['TraitDescriptor', 'ClassDescriptor'],
            ['MethodDescriptor'],
            ['FunctionDescriptor'],
            ['PropertyDescriptor'],
            ['ConstantDescriptor'],
        ];
    }
}
