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
 * Test for the FunctionDescriptor URL Generator with the Standard Router
 */
class FunctionDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\FunctionDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForFunctionDescriptor()
    {
        // Arrange
        $fixture = new FunctionDescriptor();
        $functionDescriptorMock = m::mock('phpDocumentor\Descriptor\FunctionDescriptor');
        $functionDescriptorMock->shouldReceive('getNamespace')->andReturn('My\\Space');
        $functionDescriptorMock->shouldReceive('getName')->andReturn('myFunction');

        // Act
        $result = $fixture($functionDescriptorMock);

        // Assert
        $this->assertSame('/namespaces/My.Space.html#function_myFunction', $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\FunctionDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForFunctionDescriptorWithGlobalNamespace()
    {
        // Arrange
        $fixture = new FunctionDescriptor();
        $functionDescriptorMock = m::mock('phpDocumentor\Descriptor\FunctionDescriptor');
        $functionDescriptorMock->shouldReceive('getNamespace')->andReturn('\\');
        $functionDescriptorMock->shouldReceive('getName')->andReturn('myFunction');

        // Act
        $result = $fixture($functionDescriptorMock);

        // Assert
        $this->assertSame('/namespaces/default.html#function_myFunction', $result);
    }
}
