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

namespace phpDocumentor\Compiler;

use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Represents a single pass / business rule to be executed by the Compiler.
 */
interface CompilerPassInterface
{
    /**
     * Returns a textual description of what this pass does for output purposes.
     *
     * Please note that the command line will be truncated to 68 characters (<message> .. 000.000s) so longer
     * descriptions won't have much use.
     */
    public function getDescription() : string;

    /**
     * Executes a compiler pass.
     *
     * This method will execute the business logic associated with a given compiler pass and allow it to manipulate
     * or consumer the Object Graph using the ProjectDescriptor object.
     *
     * @param ProjectDescriptor $project Representation of the Object Graph that can be manipulated.
     */
    public function execute(ProjectDescriptor $project) : void;
}
