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

use phpDocumentor\DomainModel\Path;

/**
 * Test case for Document
 * @coversDefaultClass phpDocumentor\Guides\Document
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTitle
     * @covers ::getPath
     * @covers ::getContent
     */
    public function testGetters()
    {
        $document = new DummyDocument(new Path('my/Path'), 'SomeTitle', 'MyContent');
        $this->assertEquals('SomeTitle', $document->getTitle());
        $this->assertEquals(new Path('my/Path'), $document->getPath());
        $this->assertEquals('MyContent', $document->getContent());
    }
}
