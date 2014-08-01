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

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;

/**
 * The Table of Contents File describes a file and the headings it contains.
 *
 * A File may also contain other files, those will serve as containers for more headings or other files. This way it is
 * possible to 'include' another File as part of a hierarchy and have a integrated table of contents.
 */
class File extends BaseEntry
{
    protected $hash = null;

    /**
     * The name for this file relative to the project's root.
     *
     * This name may be used to generate links and to find other file definitions in the file index of the modules.
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Sets the name for this file relative to the project root.
     *
     * @param string $filename
     *
     * @return void
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Returns the name for this file relative to the project root.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    public function getHash()
    {
        if (!$this->hash) {
            $this->hash = microtime(true);
        }

        return $this->hash;
    }
}
