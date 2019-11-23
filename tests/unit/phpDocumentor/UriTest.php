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

use PHPUnit\Framework\TestCase;

/**
 * Test case for Uri
 *
 * @coversDefaultClass \phpDocumentor\Uri
 */
final class UriTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::<private>
     */
    public function testItShouldReturnTheUriAsAString()
    {
        $uri = new Uri('http://foo.bar/phpdoc.xml');

        $this->assertSame('http://foo.bar/phpdoc.xml', (string) $uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage http://foo,bar is not a valid uri
     */
    public function testItShouldDiscardAnInvalidUri()
    {
        new Uri('http://foo,bar');
    }

    /**
     * @covers ::<private>
     */
    public function testItShouldAddAFileSchemeWhenSchemeIsAbsent()
    {
        $uri = new Uri('foo/phpdoc.xml');

        $this->assertSame('file://foo/phpdoc.xml', (string) $uri);
    }

    /**
     * @covers ::<private>
     */
    public function testItShouldAddAFileSchemeWhenAWindowsDriveLetterIsGiven()
    {
        $uri = new Uri('c:\foo\phpdoc.xml');

        $this->assertSame('file:///c:\foo\phpdoc.xml', (string) $uri);
    }

    public function testItShouldReturnTrueIfUrisAreEqual()
    {
        $uri1 = new Uri('foo');
        $uri2 = new Uri('foo');

        $this->assertTrue($uri1->equals($uri2));
    }

    public function testItShouldReturnTrueIfUrisAreNotEqual()
    {
        $uri1 = new Uri('foo');
        $uri2 = new Uri('bar');

        $this->assertFalse($uri1->equals($uri2));
    }
}
