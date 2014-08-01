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
 * Test for the PackageDescriptor URL Generator with the Standard Router
 */
class PackageDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\PackageDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromPackage
     */
    public function testGenerateUrlForPackageDescriptor()
    {
        // Arrange
        $fixture = new PackageDescriptor();
        $PackageDescriptorMock = m::mock('phpDocumentor\Descriptor\PackageDescriptor');
        $PackageDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space_Package');

        // Act
        $result = $fixture($PackageDescriptorMock);

        // Assert
        $this->assertSame('/packages/My.Space.Package.html', $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\PackageDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromPackage
     */
    public function testGenerateUrlForPackageDescriptorWithGlobalNamespace()
    {
        // Arrange
        $fixture = new PackageDescriptor();
        $PackageDescriptorMock = m::mock('phpDocumentor\Descriptor\PackageDescriptor');
        $PackageDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('\\');

        // Act
        $result = $fixture($PackageDescriptorMock);

        // Assert
        $this->assertSame('/packages/default.html', $result);
    }
}
