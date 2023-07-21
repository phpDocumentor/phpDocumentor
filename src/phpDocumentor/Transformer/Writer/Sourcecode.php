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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;

/**
 * Sourcecode transformation writer; generates syntax highlighted source files in a destination's subfolder.
 */
class Sourcecode extends WriterAbstract
{
    use IoTrait;

    public function __construct(private readonly PathGenerator $pathGenerator)
    {
    }

    public function getName(): string
    {
        return 'sourcecode';
    }

    /**
     * This method writes every source code entry in the structure file to a highlighted file.
     *
     * @param Transformation    $transformation Transformation to execute.
     * @param ProjectDescriptor $project        Document containing the structure.
     */
    public function transform(
        Transformation $transformation,
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
    ): void {
        if ($documentationSet instanceof ApiSetDescriptor === false) {
            return;
        }

        /** @var FileDescriptor $file */
        foreach ($documentationSet->getFiles() as $file) {
            $source = $file->getSource();
            if ($source === null) {
                continue;
            }

            $path = $this->pathGenerator->generate($file, $transformation);
            $this->persistTo($transformation, $path, $source);
        }
    }
}
