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
 * Test for the ClassDescriptor URL Generator with the Standard Router
 */
class ClassDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\ClassDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromClass
     */
    public function testGenerateUrlForClassDescriptor()
    {
        // Arrange
        $fixture = new ClassDescriptor();
        $classDescriptorMock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('My\\Space\\Class');

        // Act
        $result = $fixture($classDescriptorMock);

        // Assert
        $this->assertSame('/classes/My.Space.Class.html', $result);
    }
}
