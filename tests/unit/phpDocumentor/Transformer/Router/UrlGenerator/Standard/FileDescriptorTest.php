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
 * Test for the FileDescriptor URL Generator with the Standard Router
 */
class FileDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @codingStandardsIgnoreStart
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\FileDescriptor::__invoke
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromFile
     * @covers phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::removeFileExtensionFromPath
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForFileDescriptor()
    {
        // Arrange
        $fixture = new FileDescriptor();
        $FileDescriptorMock = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $FileDescriptorMock->shouldReceive('getPath')->andReturn('My/Space/Class.php');

        // Act
        $result = $fixture($FileDescriptorMock);

        // Assert
        $this->assertSame('/files/My.Space.Class.html', $result);
    }
}
