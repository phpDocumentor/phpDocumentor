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

namespace phpDocumentor\Plugin\Scrybe\Converter\Format;

/**
 * Defines a conversion format in Scrybe.
 */
class Format
{
    const MARKDOWN = 'markdown';
    const JSON     = 'json';
    const RST      = 'rst';
    const HTML     = 'html';
    const LATEX    = 'latex';
    const PDF      = 'pdf';
    const DOCBOOK  = 'docbook';

    /** @var string the name for this format, usually any of the constants in this class */
    protected $name;

    /** @var string the mime-type used for this format, i.e. application/json */
    protected $mime_type;

    /** @var string[] a series of file extensions that are commonly associated with this type of file */
    protected $extensions;

    /**
     * Initializes a new format.
     *
     * @param string          $name
     * @param string          $mime_type
     * @param string|string[] $extensions
     */
    public function __construct($name, $mime_type, $extensions)
    {
        if (!is_array($extensions)) {
            $extensions = (array) $extensions;
        }

        $this->setName($name);
        $this->setMimeType($mime_type);
        $this->setExtensions($extensions);
    }

    /**
     * Sets the name for this format.
     *
     * The names of built-in formats are defined as class constants of this class.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name for this format.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the mime type commonly associated with files of this format.
     *
     * @param string $mime_type
     *
     * @return void
     */
    public function setMimeType($mime_type)
    {
        $this->mime_type = $mime_type;
    }

    /**
     * Returns the Mime type commonly associated with files of this format.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mime_type;
    }

    /**
     * Sets the file extensions commonly associated with files of this format.
     *
     * @param string[] $extensions
     *
     * @return void
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Returns the file extensions commonly associated with files of this format.
     *
     * @return string[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Converts the given filename to be math this format.
     *
     * @param string $filename
     *
     * @return string
     */
    public function convertFilename($filename)
    {
        return substr($filename, 0, strrpos($filename, '.')).'.' . reset($this->extensions);
    }
}
