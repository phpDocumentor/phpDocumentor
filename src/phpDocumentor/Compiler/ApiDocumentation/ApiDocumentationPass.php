<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\ApiDocumentation;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;
use ReflectionObject;

use function current;

abstract class ApiDocumentationPass implements CompilerPassInterface
{
    final public function __invoke(CompilableSubject $subject): CompilableSubject
    {
        if ($subject instanceof ApiSetDescriptor) {
            return $this->process($subject);
        }

        return $subject;
    }

    /**
     * Actual method executed by the compiler.
     *
     * Processes the given ApiSetDescriptor and returns the modified {@see ApiSetDescriptor}.
     */
    abstract protected function process(ApiSetDescriptor $subject): ApiSetDescriptor;

    public function getDescription(): string
    {
        $self = new ReflectionObject($this);
        $attributes = $self->getAttributes(Stage::class);

        $stageDescription = current($attributes);
        if ($stageDescription === false) {
            return '';
        }

        return $stageDescription->newInstance()->description ?? '';
    }
}
