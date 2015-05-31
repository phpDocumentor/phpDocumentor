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
use phpDocumentor\Path;

/**
 * Base class for Guide Documents.
 */
abstract class Document
{
    /**
     * @var string title of the document.
     */
    private $title;

    /**
     * @var string content of the document.
     */
    private $content;

    /**
     * @var Path path to the source file.
     */
    private $path;


    /**
     * Initializes the document.
     *
     * @param Path $path
     * @param string $title
     * @param string $content
     */
    public function __construct(Path $path, $title, $content)
    {
        $this->path = $path;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Returns the type of the document.
     *
     * @return ContentType
     */
    abstract public function getContentType();

    /**
     * Returns the title of the document.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the content of the document.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the path to the source file.
     *
     * @return Path
     */
    public function getPath()
    {
        return $this->path;
    }
}
