<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Transformer\Exception;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Writer containing file system operations.
 *
 * The Query part of the transformation determines the action, currently
 * supported is:
 *
 * * copy, copies a file or directory to the destination given in $artifact
 * * append, copies a file or directory and appends it to the destination given in $artifact; if $artifact does not
 *   exist yet it is created.
 */
class FileIo extends WriterAbstract
{
    /** @var \phpDocumentor\Transformer\Transformation */
    protected $transformation = null;

    /**
     * Invokes the query method contained in this class.
     *
     * @param ProjectInterface $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @throws \InvalidArgumentException if the query is not supported.
     *
     * @return void
     */
    public function transform(ProjectInterface $project, Transformation $transformation)
    {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $transformation->setArtifact($artifact);

        $method = 'executeQuery' . ucfirst($transformation->getQuery());
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(
                'The query ' . $method . ' is not supported by the FileIo writer, '
                . 'supported operations are "copy" and "append"'
            );
        }

        $this->$method($transformation);
    }

    /**
     * Copies files or folders to the Artifact location.
     *
     * @param Transformation $transformation Transformation to use as data source.
     *
     * @throws Exception if the source location cannot be read
     * @throws Exception if the target location is not writable
     *
     * @return void
     */
    public function executeQueryCopy(Transformation $transformation)
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
            $filesystem->mirror($path, $transformation->getArtifact(), null, array('override' => true));
        }
    }

    /**
     * Appends the contents of the source file to the target file.
     *
     * @param Transformation $transformation
     *
     * @throws Exception
     *
     * @return void
     */
    public function executeQueryAppend(Transformation $transformation)
    {
        $target = $transformation->getArtifact();
        $path = $transformation->getSourceAsPath();

        if (!is_readable($path)) {
            throw new Exception('Unable to read the source file: ' . $path);
        }
        if (!is_file($target)) {
            throw new Exception(
                'Unable to write to "' . $target . '", expected a file but received a folder'
            );
        }

        file_put_contents($target, file_get_contents($path), FILE_APPEND);
    }
}
