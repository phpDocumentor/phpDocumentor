<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\ApiDocumentation;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiSetDescriptor;

abstract class ApiDocumentationPass implements CompilerPassInterface
{
    final public function __invoke(CompilableSubject $subject): CompilableSubject
    {
        if ($subject instanceof ApiSetDescriptor) {
            return $this->process($subject);
        }

        return $subject;
    }

    abstract protected function process(ApiSetDescriptor $subject): ApiSetDescriptor;
}
