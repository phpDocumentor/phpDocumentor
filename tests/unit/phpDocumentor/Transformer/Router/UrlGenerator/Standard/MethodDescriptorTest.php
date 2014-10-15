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
 * Test for the MethodDescriptor URL Generator with the Standard Router
 */
class MethodDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\MethodDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromClass
     */
    public function testGenerateUrlForMethodDescriptor()
    {
        // Arrange
        $fixture = new MethodDescriptor();
        $methodDescriptorMock = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $methodDescriptorMock
            ->shouldReceive('getParent->getFullyQualifiedStructuralElementName')
            ->andReturn('My\\Space\\Class');
        $methodDescriptorMock->shouldReceive('getName')->andReturn('myMethod');

        // Act
        $result = $fixture($methodDescriptorMock);

        // Assert
        $this->assertSame('/classes/My.Space.Class.html#method_myMethod', $result);
    }
}
