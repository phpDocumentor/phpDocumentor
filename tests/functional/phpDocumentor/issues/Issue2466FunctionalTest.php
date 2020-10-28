<?php

declare(strict_types=1);

namespace functional\phpDocumentor\issues;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\FunctionalTestCase;

final class Issue2466FunctionalTest extends FunctionalTestCase
{
    public function testPropertyXmlContainsExpectedValues() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/issues/issue-2466/issue-2466.php', ['--template=xml', '-tout', ]);
        $xml = $this->loadContents('out/structure.xml');

        $this->assertStringContainsString('<default>&#039;default&#039;</default>', $xml);;
        $this->assertStringContainsString('<description>This is a description of the property</description>', $xml);;
        $this->assertStringContainsString('<long-description>Here is more detailed explanation of what this property is for.</long-description>', $xml);;
    }
}
