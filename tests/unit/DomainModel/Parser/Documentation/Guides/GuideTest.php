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

namespace phpDocumentor\DomainModel\Parser\Documentation\Guides;

use Mockery as m;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\DomainModel\Parser\Documentation\Guides\Guide;
use phpDocumentor\DomainModel\Path;

/**
 * Test case for Guide
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Documentation\Guides\Guide
 */
class GuideTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getFormat
     */
    public function testGetFormat()
    {
        $documentGroupFormat = new DocumentGroupFormat('api');
        $guide = new Guide($documentGroupFormat);

        $this->assertSame($documentGroupFormat, $guide->getFormat());
    }

    /**
     * @covers ::__construct
     * @covers ::findDocumentByPath
     */
    public function testFindNotExistingDocumentByPath()
    {
        $documentGroupFormat = new DocumentGroupFormat('api');
        $guide = new Guide($documentGroupFormat);

        $this->assertNull($guide->findDocumentByPath(new Path('my/Path')));
    }

    /**
     * @covers ::__construct
     * @covers ::findDocumentByPath
     * @covers ::addDocument
     */
    public function testFindDocumentByPath()
    {
        $path = new Path('my/Path');
        $document = new DummyDocument($path, 'MyTitle', 'Content');
        $documentGroupFormat = new DocumentGroupFormat('api');
        $guide = new Guide($documentGroupFormat);

        $guide->addDocument($document);

        $this->assertSame($document, $guide->findDocumentByPath($path));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocuments
     * @covers ::addDocument
     */
    public function testGetElements()
    {
        $path = new Path('my/Path');
        $document = new DummyDocument($path, 'MyTitle', 'Content');
        $documentGroupFormat = new DocumentGroupFormat('api');
        $guide = new Guide($documentGroupFormat);

        $guide->addDocument($document);

        $this->assertEquals(
            [
                'my/Path' => $document,
            ],
            $guide->getDocuments()
        );
    }
}
