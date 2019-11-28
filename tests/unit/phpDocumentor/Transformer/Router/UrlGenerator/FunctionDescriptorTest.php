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

namespace phpDocumentor\Transformer\Router\UrlGenerator;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the FunctionDescriptor URL Generator with the Standard Router
 * @coversDefaultClass \phpDocumentor\Transformer\Router\UrlGenerator\FunctionDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
class FunctionDescriptorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForFunctionDescriptor(): void
    {
        // Arrange
        $expected = '/namespaces/My.Space.html#function_myFunction';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new FunctionDescriptor($urlGenerator, $converter);
        $functionDescriptorMock = m::mock('phpDocumentor\Descriptor\FunctionDescriptor');
        $functionDescriptorMock->shouldReceive('getNamespace')->andReturn('My\\Space');
        $functionDescriptorMock->shouldReceive('getName')->andReturn('myFunction');

        // Act
        $result = $fixture($functionDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForFunctionDescriptorWithGlobalNamespace(): void
    {
        // Arrange
        $expected = '/namespaces/default.html#function_myFunction';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();

        $fixture = new FunctionDescriptor($urlGenerator, $converter);
        $functionDescriptorMock = m::mock('phpDocumentor\Descriptor\FunctionDescriptor');
        $functionDescriptorMock->shouldReceive('getNamespace')->andReturn('\\');
        $functionDescriptorMock->shouldReceive('getName')->andReturn('myFunction');

        // Act
        $result = $fixture($functionDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
