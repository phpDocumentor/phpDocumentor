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
use phpDocumentor\Descriptor\NamespaceDescriptor as NamespaceDescriptorAlias;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the NamespaceDescriptor URL Generator with the Standard Router
 * @coversDefaultClass \phpDocumentor\Transformer\Router\UrlGenerator\NamespaceDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
class NamespaceDescriptorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForNamespaceDescriptor(): void
    {
        // Arrange
        $expected = '/namespaces/My.Space.html';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new NamespaceDescriptor($urlGenerator, $converter);
        $NamespaceDescriptorMock = m::mock(NamespaceDescriptorAlias::class);
        $NamespaceDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space');

        // Act
        $result = $fixture($NamespaceDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForNamespaceDescriptorWithGlobalNamespace(): void
    {
        // Arrange
        $expected = '/namespaces/default.html';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();

        $fixture = new NamespaceDescriptor($urlGenerator, $converter);
        $NamespaceDescriptorMock = m::mock(NamespaceDescriptorAlias::class);
        $NamespaceDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('\\');

        // Act
        $result = $fixture($NamespaceDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
