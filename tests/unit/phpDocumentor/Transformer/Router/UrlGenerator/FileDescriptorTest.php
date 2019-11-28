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
 * Test for the FileDescriptor URL Generator with the Standard Router
 * @coversDefaultClass  \phpDocumentor\Transformer\Router\UrlGenerator\FileDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
class FileDescriptorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::fromFile
     * @uses \phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter::removeFileExtensionFromPath
     */
    public function testGenerateUrlForFileDescriptor(): void
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
