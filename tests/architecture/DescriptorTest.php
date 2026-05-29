<?php

declare(strict_types=1);

namespace phpDocumentor\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;

class DescriptorTest
{
    public function test_descriptor_domain_does_not_depend_on_other_layers(): Rule
    {
        return PHPAt::rule()
            ->classes(Selector::inNamespace('phpDocumenbtor\Descriptor'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('phpDocumentor\Compiler'),
                Selector::inNamespace('phpDocumentor\Configuration')
            );
    }

    public function test_descriptor_domain_should_not_be_used_in_other_layers(): Rule
    {
        return PHPAt::rule()
            ->classes(
                Selector::AND(
                    Selector::inNamespace('phpDocumentor'),
                    Selector::NOT(
                        Selector::inNamespace('phpDocumentor\Descriptor')
                    )
                )
            )
            ->shouldNotDependOn()
            ->classes(
                Selector::AND(
                    Selector::inNamespace('phpDocumentor\Descriptor'),
                    Selector::NOT(Selector::inNamespace('phpDocumentor\Descriptor\Interfaces')),
                    Selector::NOT(Selector::classname(Descriptor::class)),
                    Selector::NOT(Selector::classname(Collection::class)),
                )
            );
    }
}
