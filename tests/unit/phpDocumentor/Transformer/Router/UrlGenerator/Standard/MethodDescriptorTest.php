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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the MethodDescriptor URL Generator with the Standard Router
 */
class MethodDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\MethodDescriptor::__invoke
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromClass
     */
    public function testGenerateUrlForMethodDescriptor() : void
    {
        $expected = '/classes/My.Space.Class.html#method_myMethod';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();

        // Arrange
        $fixture = new MethodDescriptor($urlGenerator, $converter);
        $methodDescriptorMock = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $methodDescriptorMock
            ->shouldReceive('getParent->getFullyQualifiedStructuralElementName')
            ->andReturn('My\\Space\\Class');
        $methodDescriptorMock->shouldReceive('getName')->andReturn('myMethod');

        // Act
        $result = $fixture($methodDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
