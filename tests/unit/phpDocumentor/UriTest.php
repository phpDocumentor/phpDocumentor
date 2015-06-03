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

final class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testItShouldBuildAUri()
    {
        $uri = new Uri('file://foo');

        $this->assertInstanceOf('phpDocumentor\Uri', $uri);
        $this->assertObjectHasAttribute('uri', $uri);
    }

    public function testItShouldDiscardAnInvalidUri()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid uri');

        new Uri('foo');
    }

    public function testItShouldReturnTheUriAsAString()
    {
        $uri = new Uri('file://foo');

        $this->assertSame('file://foo', (string)$uri);
    }
}
