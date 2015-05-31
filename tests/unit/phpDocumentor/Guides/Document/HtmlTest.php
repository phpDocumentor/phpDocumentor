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

namespace phpDocumentor\Guides\Document;

use phpDocumentor\Guides\ContentType;
use phpDocumentor\Path;

/**
 * Test case for Html
 * @coversDefaultClass phpDocumentor\Guides\Document\Html
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getContent
     */
    public function testGetContentType()
    {
        $htmlDocument = new Html(new Path('my/Path'), 'MyTitle', 'content');

        $this->assertEquals(new ContentType('text/html'), $htmlDocument->getContentType());
    }
}
