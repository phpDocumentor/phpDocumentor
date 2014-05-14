<?php
/**
 * Created by PhpStorm.
 * User: mvriel
 * Date: 9-5-14
 * Time: 19:55
 */

namespace phpDocumentor\Configuration\Transformations;

use JMS\Serializer\Annotation as Serializer;

class Template
{
    /**
     * @var string
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     */
    protected $name;
} 