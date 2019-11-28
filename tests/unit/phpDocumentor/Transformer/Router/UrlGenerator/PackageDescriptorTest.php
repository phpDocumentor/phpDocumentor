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
use phpDocumentor\Descriptor\PackageDescriptor as PackageDescriptorAlias;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the PackageDescriptor URL Generator with the Standard Router
 * @coversDefaultClass \phpDocumentor\Transformer\Router\UrlGenerator\PackageDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
class PackageDescriptorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromPackage
     */
    public function testGenerateUrlForPackageDescriptor(): void
    {
        // Arrange
        $expected = '/packages/My.Space.Package.html';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new PackageDescriptor($urlGenerator, $converter);
        $PackageDescriptorMock = m::mock(PackageDescriptorAlias::class);
        $PackageDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space_Package');

        // Act
        $result = $fixture($PackageDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromPackage
     */
    public function testGenerateUrlForPackageDescriptorWithGlobalNamespace(): void
    {
        // Arrange
        $expected = '/packages/default.html';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();

        $fixture = new PackageDescriptor($urlGenerator, $converter);
        $PackageDescriptorMock = m::mock(PackageDescriptorAlias::class);
        $PackageDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('\\');

        // Act
        $result = $fixture($PackageDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
