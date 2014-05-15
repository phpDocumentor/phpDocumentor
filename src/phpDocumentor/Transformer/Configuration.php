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
     * @Serializer\Type("string")
     * @var string destination location for the transformer's output
     */
    protected $target;

    /**
     * @Serializer\Type("array<phpDocumentor\Transformer\Configuration\ExternalClassDocumentation>")
     * @Serializer\SerializedName("external-class-documentation")
     * @var Configuration\ExternalClassDocumentation[]
     */
    protected $externalClassDocumentation = array();

    /**
     * @return Configuration\ExternalClassDocumentation[]
     */
    public function getExternalClassDocumentation()
    {
        return $this->externalClassDocumentation;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
}
