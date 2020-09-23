<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Tag;


use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use Webmozart\Assert\Assert;

class InheritDescriptionTagDescriptor implements Tag, Descriptor
{
    /**
     * @var Description
     */
    private $description;

    public function getName() : string
    {
        return 'inheritDoc';
    }

    public function __construct(Description $description)
    {
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $body, Description $inheritedDescription = null)
    {
        Assert::isInstanceOf($inheritedDescription, Description::class);
        return new self($inheritedDescription);
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function render(?Formatter $formatter = null) : string
    {
        return $this->description->render($formatter);
    }

    public function __toString() : string
    {
        return $this->description->render();
    }
}
