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
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\Fqsen as RealFqsen;
use phpDocumentor\Transformer\Router\UrlGenerator\FqsenDescriptor;
use phpDocumentor\Transformer\Router\UrlGenerator\MethodDescriptor;
use phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouterTest extends MockeryTestCase
{
    /** @var Router */
    private $fixture;

    /** @var Collection */
    private $elementCollection;

    /**
     * Sets up the fixture.
     */
    protected function setUp(): void
    {
        $this->elementCollection = new Collection();

        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $converter = new QualifiedNameToUrlConverter();
        $builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $builder
            ->shouldReceive('getProjectDescriptor->getIndexes->get')
            ->with('elements')
            ->andReturn($this->elementCollection);

        $this->fixture = new Router(
            $builder,
            new UrlGenerator\NamespaceDescriptor($urlGenerator, $converter),
            new UrlGenerator\FileDescriptor($urlGenerator, $converter),
            new UrlGenerator\PackageDescriptor($urlGenerator, $converter),
            new UrlGenerator\ClassDescriptor($urlGenerator, $converter),
            new UrlGenerator\MethodDescriptor($urlGenerator, $converter),
            new UrlGenerator\ConstantDescriptor($urlGenerator, $converter),
            new UrlGenerator\FunctionDescriptor($urlGenerator, $converter),
            new UrlGenerator\PropertyDescriptor($urlGenerator, $converter),
            new UrlGenerator\FqsenDescriptor($urlGenerator, $converter)
        );
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::__construct
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::match
     * @dataProvider provideDescriptorNames
     */
    public function testIfARouteForAFileCanBeGenerated($descriptorName, $generatorName = null) : void
    {
        // Arrange
        $generatorName = $generatorName ?: $descriptorName;
        $file = m::mock('phpDocumentor\\Descriptor\\' . $descriptorName);

        // Act
        $rule = $this->fixture->match($file);

        // Assert
        $this->assertInstanceOf(Rule::class, $rule);

        $generatorProperty = new \ReflectionProperty(Rule::class, 'generator');
        $generatorProperty->setAccessible(true);

        $this->assertInstanceOf(
            '\phpDocumentor\\Transformer\\Router\\UrlGenerator\\' . $generatorName,
            $generatorProperty->getValue($rule)
        );
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::__construct
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::match
     */
    public function testIfARouteForAFqsenFileCanBeGenerated() : void
    {
        // Arrange
        $fqsen = new RealFqsen('\Fqsen');
        $file = new Fqsen($fqsen);

        // Act
        $rule = $this->fixture->match($file);

        // Assert
        $generatorProperty = new \ReflectionProperty(Rule::class, 'generator');
        $generatorProperty->setAccessible(true);

        $this->assertInstanceOf(Rule::class, $rule);
        $this->assertInstanceOf(FqsenDescriptor::class, $generatorProperty->getValue($rule));
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::__construct
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::match
     */
    public function testIfARouteForAUrlCanBeGenerated() : void
    {
        // Arrange
        $file = new Url('http://www.phpdoc.org');

        // Act
        $rule = $this->fixture->match($file);
        $result = $rule->generate($file);

        // Assert
        $this->assertInstanceOf(Rule::class, $rule);
        $this->assertSame('http://www.phpdoc.org', $result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::__construct
     * @covers \phpDocumentor\Transformer\Router\Router::__construct
     * @covers \phpDocumentor\Transformer\Router\Router::configure
     * @covers \phpDocumentor\Transformer\Router\Router::match
     */
    public function testIfARouteForAFqsenCanBeGenerated() : void
    {
        // Arrange
        $fqsen = '\My\ClassName::myMethod()';
        $this->elementCollection[$fqsen] = m::mock('phpDocumentor\Descriptor\MethodDescriptor');

        // Act
        $rule = $this->fixture->match($fqsen);

        // Assert
        $generatorProperty = new \ReflectionProperty(Rule::class, 'generator');
        $generatorProperty->setAccessible(true);

        $this->assertInstanceOf(Rule::class, $rule);
        $this->assertInstanceOf(MethodDescriptor::class, $generatorProperty->getValue($rule));
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Router::match
     */
    public function testGeneratingRouteForUnknownNodeReturnsFalse() : void
    {
        $this->assertFalse($this->fixture->match('Unknown')->generate('Unknown'));
    }

    /**
     * Returns the names of descriptors and generators supported by the StandardRouter.
     *
     * @return string[][]
     */
    public function provideDescriptorNames() : array
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
