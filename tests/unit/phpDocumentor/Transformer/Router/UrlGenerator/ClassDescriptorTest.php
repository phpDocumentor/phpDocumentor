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
 * Test for the ClassDescriptor URL Generator with the Standard Router
 * @coversDefaultClass \phpDocumentor\Transformer\Router\UrlGenerator\ClassDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
class ClassDescriptorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromClass
     */
    public function testGenerateUrlForClassDescriptor(): void
    {
        // Arrange
        $expected = '/classes/My.Space.Class.html';

        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new ClassDescriptor($urlGenerator, $converter);

        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $classDescriptorMock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space\\Class');

        // Act
        $result = $fixture($classDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
