<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

use PHPUnit\Framework\TestCase;

final class UmlNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_value(): void
    {
        $node = new UmlNode('value');

        $this->assertSame('value', $node->getValue());
        $this->assertSame('value', $node->getValueString());
    }

    public function test_you_can_set_a_caption_for_underneath_diagrams(): void
    {
        $caption = 'caption';

        $node = new UmlNode('value');
        $node->setCaption($caption);

        $this->assertSame($caption, $node->getCaption());
    }

    public function test_you_can_pass_classes_for_in_templates(): void
    {
        $classes = ['float-left', 'my-class'];

        $node = new UmlNode('value');
        $node->setClasses($classes);

        $this->assertSame($classes, $node->getClasses());
        $this->assertSame('float-left my-class', $node->getClassesString());
    }
}
