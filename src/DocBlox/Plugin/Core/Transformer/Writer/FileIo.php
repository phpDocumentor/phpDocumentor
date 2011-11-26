<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Writer containing file system operations.
 *
 * The Query part of the transformation determines the action, currently supported is:
 *
 * * copy, copies a file or directory to the destination given in $artifact
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Writer_FileIo
    extends DocBlox_Transformer_Writer_Abstract
{
    /** @var DocBlox_Transformer_Transformation */
    protected $transformation = null;

    /** @var DOMDocument */
    protected $structure = null;

    /**
     * Invokes the query method contained in this class.
     *
     * @throws InvalidArgumentException
     *
     * @param DOMDocument            $structure
     * @param DocBlox_Transformer_Transformation $transformation
     *
     * @return void
     */
    public function transform(DOMDocument $structure,
        DocBlox_Transformer_Transformation $transformation
    ) {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $transformation->setArtifact($artifact);

        $method = 'executeQuery' . ucfirst($transformation->getQuery());
        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException(
                'The query ' . $method . ' is not supported by the FileIo writer'
            );
        }

        $this->$method($transformation);
    }

    /**
     * Copies files or folders to the Artifact location.
     *
     * @param DocBlox_Transformer_Transformation $transformation Transformation
     *     to use as data source.
     *
     * @throws Exception
     *
     * @return void
     */
    public function executeQueryCopy(
        DocBlox_Transformer_Transformation $transformation
    ) {
        $path = $transformation->getSourceAsPath();
        if (!is_readable($path)) {
            throw new DocBlox_Transformer_Exception(
                'Unable to read the source file: ' . $path
            );
        }

        if (!is_writable($transformation->getTransformer()->getTarget())) {
            throw new DocBlox_Transformer_Exception(
                'Unable to write to: ' . dirname($transformation->getArtifact())
            );
        }

        $transformation->getTransformer()->copyRecursive(
            $path,
            $transformation->getArtifact()
        );
    }

}