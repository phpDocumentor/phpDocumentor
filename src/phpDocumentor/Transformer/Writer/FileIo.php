<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use InvalidArgumentException;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Exception;
use phpDocumentor\Transformer\Transformation;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;
use function dirname;
use function is_file;
use function is_readable;
use function is_writable;
use function method_exists;
use function ucfirst;

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
    /** @var Transformation */
    protected $transformation = null;

    /**
     * Invokes the query method contained in this class.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     *
     * @throws InvalidArgumentException If the query is not supported.
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation) : void
    {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $transformation->setArtifact($artifact);

        $method = 'executeQuery' . ucfirst($transformation->getQuery());
        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException(
                'The query ' . $method . ' is not supported by the FileIo writer, supported operation is "copy"'
            );
        }

        $this->{$method}($transformation);
    }

    /**
     * Copies files or folders to the Artifact location.
     *
     * TODO: reimplement this using flysystem.
     *
     * @param Transformation $transformation Transformation to use as data source.
     *
     * @throws Exception
     */
    public function executeQueryCopy(Transformation $transformation) : void
    {
        $path = $transformation->getSourceAsPath();

        if (!is_readable($path)) {
            throw new Exception('Unable to read the source file: ' . $path);
        }

        if (!is_writable($transformation->getTransformer()->getTarget())) {
            throw new Exception('Unable to write to: ' . dirname($transformation->getArtifact()));
        }

        $filesystem = new Filesystem();
        if (is_file($path)) {
            $filesystem->copy($path, $transformation->getArtifact(), true);
        } else {
            $filesystem->mirror($path, $transformation->getArtifact(), null, ['override' => true]);
        }
    }
}
