<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Version\Pass;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Descriptor\VersionDescriptor;

final class SetVersionPass implements CompilerPassInterface
{
    private DescriptorRepository $descriptorRepository;

    public function __construct(DescriptorRepository $descriptorRepository)
    {
        $this->descriptorRepository = $descriptorRepository;
    }

    public function getDescription(): string
    {
        return 'Prepare version in repository';
    }

    public function __invoke(CompilableSubject $subject): CompilableSubject
    {
        if ($subject instanceof VersionDescriptor === false) {
            return $subject;
        }

        $this->descriptorRepository->setVersionDescriptor($subject);

        return $subject;
    }
}
