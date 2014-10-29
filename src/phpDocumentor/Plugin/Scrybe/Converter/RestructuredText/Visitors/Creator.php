<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors;

use phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;
use phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Document;

/**
 * A specialized RestructuredText Parser/Visitor to provide assistance methods for the creation phase..
 */
class Creator extends \ezcDocumentRstXhtmlBodyVisitor
{
    /** @var Document */
    protected $rst;

    /**
     * Returns the table of contents.
     *
     * return TableOfContents $toc
     */
    public function getTableOfContents()
    {
        return $this->getDocument()->getConverter()->getTableOfContents();
    }

    /**
     * Returns the filename for this visitor.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->getDocument()->getConverter()
            ->getDestinationFilenameRelativeToProjectRoot($this->getDocument()->getFile());
    }

    /**
     * Returns the filename for this visitor without an extension.
     *
     * @return string
     */
    public function getFilenameWithoutExtension()
    {
        $filename = $this->getDocument()->getFile()->getFilename();

        return substr($filename, 0, strrpos($filename, '.'));
    }

    /**
     * Returns the RestructuredText Document to retrieve the specialized cross-document collections.
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->rst;
    }
}
