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

namespace phpDocumentor\Transformer\Configuration\Transformations;

use JMS\Serializer\Annotation as Serializer;

/**
 * Configuration object for a template selection.
 */
class Template
{
    /**
     * @var string the configured name of the current template.
     *
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     */
    protected $name;

    /**
     * Registers the name of the template with this object.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the template name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
