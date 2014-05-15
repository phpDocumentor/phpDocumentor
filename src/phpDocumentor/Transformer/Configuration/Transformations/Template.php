<?php

namespace phpDocumentor\Transformer\Configuration\Transformations;

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