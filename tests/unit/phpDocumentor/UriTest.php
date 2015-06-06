<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

/**
 * Test case for Uri
 *
 * @coversDefaultClass phpDocumentor\Uri
 */
final class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::validateString
     * @covers ::validateUri
     */
    public function testItShouldBuildAUri()
    {
        $uri = new Uri('file://foo');

        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertObjectHasAttribute('uri', $uri);
    }

    /**
     * @covers ::validateString
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage String required, array given
     */
    public function testItShouldOnlyAcceptStrings()
    {
        new Uri([]);
    }

    /**
     * @covers ::validateUri
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage http://foo,bar is not a valid uri
     */
    public function testItShouldDiscardAnInvalidUri()
    {
        new Uri('http://foo,bar');
    }

    /**
     * @covers ::__toString
     */
    public function testItShouldReturnTheUriAsAString()
    {
        $uri = new Uri('http://foo.bar/phpdoc.xml');

        $this->assertSame('http://foo.bar/phpdoc.xml', (string) $uri);
    }

    /**
     * @covers ::checkScheme
     */
    public function testItShouldRecogniseALocalPath()
    {
        $uri = new Uri('foo/phpdoc.xml');

        $this->assertSame('file://foo/phpdoc.xml', (string) $uri);
    }
}
