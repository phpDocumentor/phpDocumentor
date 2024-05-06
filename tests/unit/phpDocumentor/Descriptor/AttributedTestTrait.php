<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ChildInterface;

trait AttributedTestTrait
{
    abstract private function getParent(): ChildInterface|null;

    public function testAddAttribute(): void
    {
        $attribute = new AttributeDescriptor();
        $this->fixture->addAttribute($attribute);

        $this->assertCount(1, $this->fixture->getAttributes());
    }

    public function testGetInheritedAttributesReturnsAttributesFromParent(): void
    {
        $parent = $this->getParent();
        $expected = 0;
        if ($parent !== null) {
            $parent->addAttribute(new AttributeDescriptor());
            $this->fixture->setParent($parent);
            $expected = 1;
        }

        $this->assertCount($expected, $this->fixture->getInheritedAttributes());
    }

    public function testGetAttributesReturnsEmptyCollection(): void
    {
        $this->assertCount(0, $this->fixture->getAttributes());
    }
}
