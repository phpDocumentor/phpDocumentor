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

namespace phpDocumentor\Plugin\Scrybe\Converter\Definition;

use phpDocumentor\Plugin\Scrybe\Converter\Format;

/**
 * Defines the basic properties for a single conversion process.
 */
class Definition
{
    /** @var Format\Format */
    protected $input_format;

    /** @var Format\Format */
    protected $output_format;

    public function __construct(Format\Format $input_format, Format\Format $output_format)
    {
        $this->input_format = $input_format;
        $this->output_format = $output_format;
    }

    /**
     * Returns the format used as input.
     *
     * @return Format\Format
     */
    public function getInputFormat()
    {
        return $this->input_format;
    }

    /**
     * Returns the format used as output.
     *
     * @return Format\Format
     */
    public function getOutputFormat()
    {
        return $this->output_format;
    }
}
