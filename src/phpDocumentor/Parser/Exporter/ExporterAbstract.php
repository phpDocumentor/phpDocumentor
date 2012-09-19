<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter;

use phpDocumentor\Parser;

/**
 * Class responsible for writing the results of the Reflection of a single
 * source file to disk.
 */
abstract class ExporterAbstract extends Parser\ParserAbstract
{
    /**
     * Parser object containing all properties used during the parsing process
     * and provided to influence export process.
     *
     * @var \phpDocumentor\Parser\Parser
     */
    protected $parser = null;

    /**
     * Whether to include the file's source in the export.
     *
     * @var bool
     */
    protected $include_source = false;

    /**
     * Construct the object with the location where to write the structure file(s).
     *
     * @param \phpDocumentor\Parser\Parser $parser
     */
    public function __construct(Parser\Parser $parser)
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
     * @param \phpDocumentor\Reflection\FileReflector $file File to export.
     *
     * @return void
     */
    abstract public function export($file);

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
     *
     * @return void
     */
    public function setIncludeSource($include_source)
    {
        $this->include_source = $include_source;
    }

    /**
     * Returns whether to include the source code in the resulting files.
     *
     * @return boolean
     */
    public function getIncludeSource()
    {
        return $this->include_source;
    }
}
