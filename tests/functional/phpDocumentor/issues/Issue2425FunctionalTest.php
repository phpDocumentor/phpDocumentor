<?php

declare(strict_types=1);

namespace functional\phpDocumentor\issues;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\FunctionalTestCase;

final class Issue2425FunctionalTest extends FunctionalTestCase
{
    public function testInlineAliasedSeeIsResolved() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/issues/issue-2425/issue-2425.php');
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Project\Sub\Level\Issue2425A');

        self::assertSame(
            <<<DESCRIPTION
A description containing an inline \Project\Other\Level\Issue2425B::bar() tag
to a class inside of the project referenced via a use statement.

And here is another inline \Project\Other\Level\Issue2425C::bar() tag to a class
aliased via a use statement.
DESCRIPTION,
            $classDescriptor->getDescription());
    }
}
