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
use phpDocumentor\Guides\Document;
use phpDocumentor\Path;

final class Html extends Document
{
    /**
     * Content type of this document.
     * Will be text/html.
     *
     * @var ContentType
     */
    private $contentType;

    /**
     * Initializes the object.
     *
     * @param Path $path
     * @param string $title
     * @param string $content
     */
    public function __construct(Path $path, $title, $content)
    {
        parent::__construct($path, $title, $content);
        $this->contentType = new ContentType('text/html');
    }

    /**
     * Returns the type of the document.
     *
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
