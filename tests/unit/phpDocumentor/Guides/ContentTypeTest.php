<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Guides;


/**
 * Test case for ContentType.
 * @coversDefaultClass phpDocumentor\Guides\ContentType
 */
class ContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToString()
    {
        $contentType = new ContentType('text/html');
        $this->assertEquals('text/html', (string)$contentType);
    }
}