<?php

namespace phpDocumentor\Configuration\Transformer;

use JMS\Serializer\Annotation as Serializer;

class ExternalClassDocumentation
{
    /**
     * @Serializer\Type("string")
     * @var string
     */
    protected $prefix;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    protected $uri;

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
} 