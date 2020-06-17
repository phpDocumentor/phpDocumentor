<?php

declare(strict_types=1);

namespace functional\phpDocumentor\core;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\FunctionalTestCase;

final class ConstantFunctionalTest extends FunctionalTestCase
{
    public function testConstantValue() : void
    {
        $this->runPHPDocWithFile( __DIR__ . '/../../assets/core/constant/issue-2421.php');
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Issue2421');

        /** @var ConstantDescriptor $constant */
        $constant = $classDescriptor->getConstants()->get('PHP_LABEL_REGEX');

        $this->assertSame("'`^[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*$`'", $constant->getValue());
    }
}
