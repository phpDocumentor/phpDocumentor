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
use phpDocumentor\Descriptor\PropertyDescriptor as PropertyDescriptorAlias;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the PropertyDescriptor URL Generator with the Standard Router
 * @coversDefaultClass \phpDocumentor\Transformer\Router\UrlGenerator\PropertyDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
class PropertyDescriptorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromClass
     */
    public function testGenerateUrlForPropertyDescriptor(): void
    {
        // Arrange
        $expected = '/classes/My.Space.Class.html#property_myProperty';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new PropertyDescriptor($urlGenerator, $converter);
        $propertyDescriptorMock = m::mock(PropertyDescriptorAlias::class);
        $propertyDescriptorMock
            ->shouldReceive('getParent->getFullyQualifiedStructuralElementName')
            ->andReturn('My\\Space\\Class');
        $propertyDescriptorMock->shouldReceive('getName')->andReturn('myProperty');

        // Act
        $result = $fixture($propertyDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
