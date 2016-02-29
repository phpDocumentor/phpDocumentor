<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Documentation\Guides\Document;

use phpDocumentor\DomainModel\Parser\Documentation\Guides\ContentType;
use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Documentation\Guides\Document\Html
 * @covers ::__construct
 * @covers ::<private>
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::getContentType
     */
    public function itRegistersThePathNameAndContentForThisDocument()
    {
        $htmlDocument = new Html(new Path('my/Path'), 'MyTitle', 'content');

        $this->assertEquals(new ContentType('text/html'), $htmlDocument->getContentType());
    }
}
