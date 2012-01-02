<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parser\Exporter
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Class responsible for writing the results of the Reflection of a single
 * source file to disk.
 *
 * @category DocBlox
 * @package  Parser\Exporter
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
abstract class DocBlox_Parser_Exporter_Abstract extends DocBlox_Parser_Abstract
{
    /** @var \DocBlox_Parser */
    protected $parser = null;

    /** @var bool Whether to include the file's source in the export */
    protected $include_source = false;

    /**
     * Construct the object with the location where to write the structure file(s).
     *
     * @param DocBlox_Parser $parser
     */
    public function __construct(DocBlox_Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Initializes this exporter.
     *
     * @return void
     */
    public function initialize()
    {

    }

    /**
     * Renders the reflected file to a structure file.
     *
     * @param DocBlox_Reflection_File $file File to export.
     *
     * @return void
     */
    abstract public function export(DocBlox_Reflection_File $file);

    /**
     * Finalizes this exporter; performs cleaning operations.
     *
     * @return void
     */
    public function finalize()
    {

    }

    /**
     * Returns the contents of this export or null if contents were directly
     * written to disk.
     *
     * @return string|null
     */
    abstract public function getContents();

    /**
     * Sets whether to include the source in the structure files.
     *
     * @param boolean $include_source
     */
    public function setIncludeSource($include_source)
    {
        $this->include_source = $include_source;
    }
}
