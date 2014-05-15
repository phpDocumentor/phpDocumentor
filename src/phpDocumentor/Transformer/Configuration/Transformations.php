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

namespace phpDocumentor\Transformer\Configuration;

use JMS\Serializer\Annotation as Serializer;
use phpDocumentor\Configuration\Merger\Annotation as Merger;
use phpDocumentor\Transformer\Transformation;

/**
 * Represents the settings in the phpdoc.xml related to finding the files that are to be parsed.
 */
class Transformations
{
    /**
     * @var Transformations\Template[]
     * @Serializer\Type("array<phpDocumentor\Transformer\Configuration\Transformations\Template>")
     * @Serializer\XmlList(inline = true, entry = "template")
     * @Merger\Replace
     */
    protected $templates;

    /**
     * @var Transformation[]
     * @Serializer\Type("array<phpDocumentor\Transformer\Transformation>")
     * @Serializer\XmlList(inline = true, entry = "transformation")
     * @Merger\Replace
     */
    protected $transformations;

    /**
     * @return Transformations\Template[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @return \phpDocumentor\Transformer\Transformation[]
     */
    public function getTransformations()
    {
        return $this->transformations;
    }
}
