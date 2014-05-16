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
 * Contains the templates and custom transformations that are applied during transformation.
 */
class Transformations
{
    /**
     * @var Transformations\Template[] a list of templates that should be applied during the transformation process.
     *
     * @Serializer\Type("array<phpDocumentor\Transformer\Configuration\Transformations\Template>")
     * @Serializer\XmlList(inline = true, entry = "template")
     * @Merger\Replace
     */
    protected $templates;

    /**
     * @var Transformation[] list of transformations that should be applied during transformation.
     *
     * @Serializer\Type("array<phpDocumentor\Transformer\Transformation>")
     * @Serializer\XmlList(inline = true, entry = "transformation")
     * @Merger\Replace
     */
    protected $transformations;

    /**
     * Initializes this transformations configuration with a list of templates and transformations.
     *
     * @param Transformations\Template[] $templates
     * @param Transformation[]           $transformations
     */
    public function __construct($templates = array(), $transformations = array())
    {
        $this->templates       = $templates;
        $this->transformations = $transformations;
    }

    /**
     * Returns a list of templates that should be applied during the transformation process.
     *
     * @return Transformations\Template[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Returns a list of transformations that should be applied during transformation after the templates have
     * been processed.
     *
     * @return Transformation[]
     */
    public function getTransformations()
    {
        return $this->transformations;
    }
}
