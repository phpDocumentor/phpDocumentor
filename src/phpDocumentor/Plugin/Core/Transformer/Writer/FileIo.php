<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * Writer containing file system operations.
 *
 * The Query part of the transformation determines the action, currently
 * supported is:
 *
 * * copy, copies a file or directory to the destination given in $artifact
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class FileIo extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    /** @var \phpDocumentor\Transformer\Transformation */
    protected $transformation = null;

    /** @var \DOMDocument */
    protected $structure = null;

    /**
     * Invokes the query method contained in this class.
     *
     * @param \DOMDocument                        $structure      Structure document
     *     to gather data from.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation
     *     containing the meta-data for this request.
     *
     * @throws \InvalidArgumentException if the query is not supported.
     *
     * @return void
     */
    public function transform(\DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $transformation->setArtifact($artifact);

        $method = 'executeQuery' . ucfirst($transformation->getQuery());
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(
                'The query ' . $method . ' is not supported by the FileIo writer,' .
                'supported operation is "copy"'
            );
        }

        $this->$method($transformation);
    }

    /**
     * Copies files or folders to the Artifact location.
     *
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation
     *     to use as data source.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function executeQueryCopy(
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $path = $transformation->getSourceAsPath();
        if (!is_readable($path)) {
            throw new \phpDocumentor\Transformer\Exception(
                'Unable to read the source file: ' . $path
            );
        }

        if (!is_writable($transformation->getTransformer()->getTarget())) {
            throw new \phpDocumentor\Transformer\Exception(
                'Unable to write to: ' . dirname($transformation->getArtifact())
            );
        }

        $transformation->getTransformer()->copyRecursive(
            $path,
            $transformation->getArtifact()
        );
    }

}