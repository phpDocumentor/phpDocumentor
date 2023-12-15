<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

trait AttributedTest
{
    public function testAddAttribute(): void
    {
        $attribute = new AttributeDescriptor();
        $this->fixture->addAttribute($attribute);

        $this->assertCount(1, $this->fixture->getAttributes());
    }

    public function testGetAttributesReturnsEmptyCollection(): void
    {
        $this->assertCount(0, $this->fixture->getAttributes());
    }
}
