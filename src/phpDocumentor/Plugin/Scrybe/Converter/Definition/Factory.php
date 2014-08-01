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
 * Factory class that is able to return a contract between an input and output format (Definition).
 */
class Factory
{
    /** @var Format\Collection Collection of available formats */
    protected $format_collection = null;

    /**
     * Registers the available formats for use in creating definitions.
     *
     * @param Format\Collection $formats
     */
    public function __construct(Format\Collection $formats)
    {
        $this->format_collection = $formats;
    }

    /**
     * Creates a definition of the given input and output formats.
     *
     * @param string $input_format
     * @param string $output_format
     *
     * @return Definition
     */
    public function get($input_format, $output_format)
    {
        return new Definition(
            $this->format_collection[$input_format],
            $this->format_collection[$output_format]
        );
    }
}
