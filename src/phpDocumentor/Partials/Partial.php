<?php

namespace phpDocumentor\Partials;

use JMS\Serializer\Annotation as Serializer;

class Partial
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $name;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $content;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $link;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $content Set the content for tests
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param string $link Set the link for tests
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @param string $name Set the name for tests
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
