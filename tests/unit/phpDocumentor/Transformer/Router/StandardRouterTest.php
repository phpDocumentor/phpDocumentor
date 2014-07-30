<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;

class StandardRouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var StandardRouter */
    private $fixture;

    /** @var Collection */
    private $elementCollection;

    /**
     * Sets up the fixture.
     */
    public function setUp()
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
    public function testIfARouteForAFqsenCanBeGenerated()
    {
        // Arrange
        $fqsen                           = '\My\ClassName::myMethod()';
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
        return array(
            array('FileDescriptor'),
            array('NamespaceDescriptor'),
            array('PackageDescriptor'),
            array('ClassDescriptor'),
            array('InterfaceDescriptor', 'ClassDescriptor'),
            array('TraitDescriptor', 'ClassDescriptor'),
            array('MethodDescriptor'),
            array('FunctionDescriptor'),
            array('PropertyDescriptor'),
            array('ConstantDescriptor'),
        );
    }
}
