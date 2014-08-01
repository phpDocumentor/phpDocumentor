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

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use Mockery as m;

/**
 * Test for the ConstantDescriptor URL Generator with the Standard Router
 */
class ConstantDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @codingStandardsIgnoreStart
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__construct
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::getUrlPathPrefixForGlobalConstants
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForGlobalConstants()
    {
        // Arrange
        $fixture = new ConstantDescriptor();
        $constantDescriptorMock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantDescriptorMock
            ->shouldReceive('getParent')->andReturn(m::mock('phpDocumentor\Descriptor\FileDescriptor'));
        $constantDescriptorMock->shouldReceive('getNamespace')->andReturn('My\\Space');
        $constantDescriptorMock->shouldReceive('getName')->andReturn('myConstant');

        // Act
        $result = $fixture($constantDescriptorMock);

        // Assert
        $this->assertSame('/namespaces/My.Space.html#constant_myConstant', $result);
    }

    /**
     * @codingStandardsIgnoreStart
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__construct
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::getUrlPathPrefixForGlobalConstants
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForGlobalConstantsAtRootNamespace()
    {
        // Arrange
        $fixture = new ConstantDescriptor();
        $constantDescriptorMock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantDescriptorMock->shouldReceive('getParent')->andReturnNull();
        $constantDescriptorMock->shouldReceive('getNamespace')->andReturn('\\');
        $constantDescriptorMock->shouldReceive('getName')->andReturn('myConstant');

        // Act
        $result = $fixture($constantDescriptorMock);

        // Assert
        $this->assertSame('/namespaces/default.html#constant_myConstant', $result);
    }

    /**
     * @codingStandardsIgnoreStart
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ConstantDescriptor::getUrlPathPrefixForClassConstants
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromClass
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForClassConstants()
    {
        // Arrange
        $fixture = new ConstantDescriptor();

        $classDescriptorMock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space\\Class');

        $constantDescriptorMock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantDescriptorMock->shouldReceive('getParent')->andReturn($classDescriptorMock);
        $constantDescriptorMock->shouldReceive('getName')->andReturn('myConstant');

        // Act
        $result = $fixture($constantDescriptorMock);

        // Assert
        $this->assertSame('/classes/My.Space.Class.html#constant_myConstant', $result);
    }
}
