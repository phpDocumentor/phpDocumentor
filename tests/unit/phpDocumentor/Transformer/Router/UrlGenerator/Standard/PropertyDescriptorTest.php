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
 * Test for the PropertyDescriptor URL Generator with the Standard Router
 */
class PropertyDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\PropertyDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromClass
     */
    public function testGenerateUrlForPropertyDescriptor()
    {
        // Arrange
        $fixture = new PropertyDescriptor();
        $propertyDescriptorMock = m::mock('phpDocumentor\Descriptor\PropertyDescriptor');
        $propertyDescriptorMock
            ->shouldReceive('getParent->getFullyQualifiedStructuralElementName')
            ->andReturn('My\\Space\\Class');
        $propertyDescriptorMock->shouldReceive('getName')->andReturn('myProperty');

        // Act
        $result = $fixture($propertyDescriptorMock);

        // Assert
        $this->assertSame('/classes/My.Space.Class.html#property_myProperty', $result);
    }
}
