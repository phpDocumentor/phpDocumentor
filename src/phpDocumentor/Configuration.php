<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use JMS\Serializer\Annotation as Serializer;

/**
 * The definition for the configuration of phpDocumentor.
 */
class Configuration
{
    /**
     * @var Configuration\Parser $parser
     * @Serializer\Type("phpDocumentor\Configuration\Parser")
     */
    protected $parser;

    /**
     * @var Configuration\Transformer $transformer
     * @Serializer\Type("phpDocumentor\Configuration\Transformer")
     */
    protected $transformer;

    /**
     * @var Configuration\Files files
     * @Serializer\Type("phpDocumentor\Configuration\Files")
     */
    protected $files;

    /**
     * @var string[] $plugins
     * @Serializer\Type("array<string>")
     */
    protected $plugins;

    /**
     * @var (Transformer\Template|Transformer\Transformation)[] transformations
     * @Serializer\Type("phpDocumentor\Configuration\Transformer")
     */
    protected $transformations;

    /**
     * @var string[] partials
     */
    protected $partials;
}
