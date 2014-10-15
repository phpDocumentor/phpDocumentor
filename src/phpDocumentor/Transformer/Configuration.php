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

namespace phpDocumentor\Transformer;

use JMS\Serializer\Annotation as Serializer;

/**
 * Configuration definition for the transformer.
 */
class Configuration
{
    /**
     * @var string|null the location of the product created by the Parser that is used as input for the
     *     transformation process, or when null the transformer will determine the location by reading the parser's
     *     `target` setting.
     *
     * @Serializer\Type("string")
     */
    protected $source;

    /**
     * @var string destination location for the transformer's output
     *
     * @Serializer\Type("string")
     */
    protected $target = '';

    /**
     * @var Configuration\ExternalClassDocumentation[] A series of references to external API documentation sites
     *     where classes with a specific prefix can link to.
     *
     * @Serializer\Type("array<phpDocumentor\Transformer\Configuration\ExternalClassDocumentation>")
     * @Serializer\SerializedName("external-class-documentation")
     */
    protected $externalClassDocumentation = array();

    /**
     * Returns the location where the output of the parser is located.
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source location where the parser's output can be found.
     *
     * This might, for example, be used in the Run command to read the parser's output location from the `target`
     * setting in the configuration of the parser and then, by using this method, be set.
     *
     * @param string $source
     *
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Returns the destination location where the output of the transformation process should be written to.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns the references to external documentation sites for classes not found in the parsed contents.
     *
     * @return Configuration\ExternalClassDocumentation[]
     */
    public function getExternalClassDocumentation()
    {
        return $this->externalClassDocumentation;
    }

    /**
     * Sets references to external documentation sites for classes not found in the parsed contents.
     *
     * @param Configuration\ExternalClassDocumentation[] $externalClassDocumentation
     *
     * @return void
     */
    public function setExternalClassDocumentation(array $externalClassDocumentation)
    {
        $this->externalClassDocumentation = $externalClassDocumentation;
    }
}
