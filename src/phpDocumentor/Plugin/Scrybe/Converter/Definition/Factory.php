<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
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
