<?php

declare(strict_types=1);

namespace functional\phpDocumentor\issues;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\FunctionalTestCase;

final class Issue2421FunctionalTest extends FunctionalTestCase
{
    public function testConstantEscapedValueIsProcessedCorrectly() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/issues/issue-2421/issue-2421.php');
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Issue2421');

        /** @var ConstantDescriptor $constant */
        $constant = $classDescriptor->getConstants()->get('PHP_LABEL_REGEX');

        $this->assertSame("'`^[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*$`'", $constant->getValue());
    }

    public function testPropertyEscapedDefaultValueIsProcessedCorrectly() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/issues/issue-2421/issue-2421.php');
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Issue2421');

        /** @var PropertyDescriptor $propertyDescriptor */
        $propertyDescriptor = $classDescriptor->getProperties()->get('PHP_LABEL_REGEX');

        $this->assertSame("'`^[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*$`'", $propertyDescriptor->getDefault());
    }


    public function testMethodArgumentEscapedDefaultValueIsProcessedCorrectly() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/issues/issue-2421/issue-2421.php');
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Issue2421');

        /** @var MethodDescriptor $methodDescriptor */
        $methodDescriptor = $classDescriptor->getMethods()->get('issue');

        $this->assertSame(
            "'`^[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*$`'",
            $methodDescriptor->getArguments()->get('regex')->getDefault()
        );
    }
}
