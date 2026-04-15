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

namespace phpDocumentor\Guides\Compiler;

use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Guides\Nodes\ProjectNode;

final class DescriptorAwareCompilerContext extends CompilerContext
{
    public function __construct(
        ProjectNode $projectNode,
        private readonly VersionDescriptor $versionDescriptor,
    ) {
        parent::__construct($projectNode);
    }

    public function getVersionDescriptor(): VersionDescriptor
    {
        return $this->versionDescriptor;
    }
}
