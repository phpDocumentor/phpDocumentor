<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Router\UrlGenerator\Standard;

use Mockery as m;

/**
 * Test for the FunctionDescriptor URL Generator with the Standard Router
 */
class FunctionDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @codingStandardsIgnoreStart
     * @covers phpDocumentor\Application\Renderer\Router\UrlGenerator\Standard\FunctionDescriptor::__invoke
     * @covers phpDocumentor\Application\Renderer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     * @codingStandardsIgnoreEnd
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
     * @codingStandardsIgnoreStart
     * @covers phpDocumentor\Application\Renderer\Router\UrlGenerator\Standard\FunctionDescriptor::__invoke
     * @covers phpDocumentor\Application\Renderer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     * @codingStandardsIgnoreEnd
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
