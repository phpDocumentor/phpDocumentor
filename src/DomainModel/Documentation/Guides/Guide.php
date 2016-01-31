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

namespace phpDocumentor\DomainModel\Documentation\Guides;

use phpDocumentor\DomainModel\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Documentation\Guides\Document;

/**
 * Class acts as an aggregate for documents.
 */
final class Guide
{

    /**
     * Format of the guide.
     *
     * @var DocumentGroupFormat
     */
    private $format;

    /**
     * Collection of elements in the api.
     *
     * @var Document[]
     */
    private $documents = [];

    /**
     * Initialized the class with the given format.
     *
     * @param DocumentGroupFormat $format
     */
    public function __construct(DocumentGroupFormat $format)
    {
        $this->format = $format;
    }

    /**
     * Returns the format of this guide.
     *
     * @return DocumentGroupFormat
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Will return the Document when it is available. Otherwise returns null.
     *
     * @param Path $path
     *
     * @return Document|null
     */
    public function findDocumentByPath(Path $path)
    {
        if (isset($this->documents[(string)$path])) {
            return $this->documents[(string)$path];
        }

        return null;
    }

    /**
     * Add a document to the guide
     *
     * @param Document $document
     *
     * @return void
     */
    public function addDocument(Document $document)
    {
        $this->documents[(string)$document->getPath()] = $document;
    }

    /**
     * Returns all documents of the guide.
     *
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
