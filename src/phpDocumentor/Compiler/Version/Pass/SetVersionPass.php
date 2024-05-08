<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Compiler\Version\Pass;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Descriptor\VersionDescriptor;

final class SetVersionPass implements CompilerPassInterface
{
    public function __construct(private readonly DescriptorRepository $descriptorRepository)
    {
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
