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

use InvalidArgumentException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;

use function strtolower;

/**
 * Writer containing file system operations.
 *
 * The Query part of the transformation determines the action, currently
 * supported is:
 *
 * * copy, copies a file or directory to the destination given in $artifact
 */
class FileIo extends WriterAbstract
{
    use IoTrait;

    /**
     * Invokes the query method contained in this class.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     *
     * @throws InvalidArgumentException If the query is not supported.
     * @throws FileNotFoundException If the source file does not exist or could not be read.
     * @throws FileExistsException
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation): void
    {
        $method = $transformation->getQuery();
        if (strtolower($method) !== 'copy') {
            throw new InvalidArgumentException(
                'The query ' . $method . ' is not supported by the FileIo writer, supported operation is "copy"'
            );
        }

        $this->copy($transformation, $transformation->getSource(), $transformation->getArtifact());
    }
}
