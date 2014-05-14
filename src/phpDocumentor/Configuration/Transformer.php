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

namespace phpDocumentor\Configuration;

use JMS\Serializer\Annotation as Serializer;

/**
 * Configuration definition for the transformer.
 */
class Transformer
{
    /**
     * @Serializer\Type("string")
     * @var string destination location for the transformer's output
     */
    protected $target;

    /**
     * @Serializer\Type("array<phpDocumentor\Configuration\Transformer\ExternalClassDocumentation>")
     * @Serializer\SerializedName("external-class-documentation")
     * @var Transformer\ExternalClassDocumentation[]
     */
    protected $externalClassDocumentation = array();

    /**
     * @return Transformer\ExternalClassDocumentation[]
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
