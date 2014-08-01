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
 * Test for the NamespaceDescriptor URL Generator with the Standard Router
 */
class NamespaceDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\NamespaceDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForNamespaceDescriptor()
    {
        // Arrange
        $fixture = new NamespaceDescriptor();
        $NamespaceDescriptorMock = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $NamespaceDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space');

        // Act
        $result = $fixture($NamespaceDescriptorMock);

        // Assert
        $this->assertSame('/namespaces/My.Space.html', $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\NamespaceDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromNamespace
     */
    public function testGenerateUrlForNamespaceDescriptorWithGlobalNamespace()
    {
        // Arrange
        $fixture = new NamespaceDescriptor();
        $NamespaceDescriptorMock = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $NamespaceDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('\\');

        // Act
        $result = $fixture($NamespaceDescriptorMock);

        // Assert
        $this->assertSame('/namespaces/default.html', $result);
    }
}
