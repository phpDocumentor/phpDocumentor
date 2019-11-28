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

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use Mockery as m;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the ConstantDescriptor URL Generator with the Standard Router
 */
class ConstantDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @codingStandardsIgnoreStart
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__construct
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__invoke
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::getUrlPathPrefixForGlobalConstants
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForGlobalConstants() : void
    {
        // Arrange
        $expected = '/namespaces/My.Space.html#constant_myConstant';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();

        $fixture = new ConstantDescriptor($urlGenerator, $converter);
        $constantDescriptorMock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantDescriptorMock
            ->shouldReceive('getParent')->andReturn(m::mock('phpDocumentor\Descriptor\FileDescriptor'));
        $constantDescriptorMock->shouldReceive('getNamespace')->andReturn('My\\Space');
        $constantDescriptorMock->shouldReceive('getName')->andReturn('myConstant');

        // Act
        $result = $fixture($constantDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @codingStandardsIgnoreStart
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__construct
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__invoke
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::getUrlPathPrefixForGlobalConstants
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForGlobalConstantsAtRootNamespace() : void
    {
        // Arrange
        $expected = '/namespaces/default.html#constant_myConstant';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();

        $fixture = new ConstantDescriptor($urlGenerator, $converter);
        $constantDescriptorMock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantDescriptorMock->shouldReceive('getParent')->andReturnNull();
        $constantDescriptorMock->shouldReceive('getNamespace')->andReturn('\\');
        $constantDescriptorMock->shouldReceive('getName')->andReturn('myConstant');

        // Act
        $result = $fixture($constantDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @codingStandardsIgnoreStart
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__invoke
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::getUrlPathPrefixForClassConstants
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromClass
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForClassConstants() : void
    {
        // Arrange
        $expected = '/classes/My.Space.Class.html#constant_myConstant';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new ConstantDescriptor($urlGenerator, $converter);

        $classDescriptorMock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space\\Class');

        $constantDescriptorMock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantDescriptorMock->shouldReceive('getParent')->andReturn($classDescriptorMock);
        $constantDescriptorMock->shouldReceive('getName')->andReturn('myConstant');

        // Act
        $result = $fixture($constantDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
