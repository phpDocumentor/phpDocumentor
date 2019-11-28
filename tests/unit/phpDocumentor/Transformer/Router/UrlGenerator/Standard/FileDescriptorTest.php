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

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use Mockery as m;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the FileDescriptor URL Generator with the Standard Router
 */
class FileDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @codingStandardsIgnoreStart
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\FileDescriptor::__invoke
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::fromFile
     * @covers \phpDocumentor\Transformer\Router\UrlGenerator\Standard\QualifiedNameToUrlConverter::removeFileExtensionFromPath
     * @codingStandardsIgnoreEnd
     */
    public function testGenerateUrlForFileDescriptor() : void
    {
        // Arrange
        $expected = '/files/My.Space.Class.html';
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($expected);
        $converter = new QualifiedNameToUrlConverter();
        $fixture = new FileDescriptor($urlGenerator, $converter);
        $FileDescriptorMock = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $FileDescriptorMock->shouldReceive('getPath')->andReturn('My/Space/Class.php');

        // Act
        $result = $fixture($FileDescriptorMock);

        // Assert
        $this->assertSame($expected, $result);
    }
}
