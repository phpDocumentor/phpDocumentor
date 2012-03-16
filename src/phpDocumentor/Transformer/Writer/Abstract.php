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

/**
 * Base class for the actual transformation business logic (writers).
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
abstract class phpDocumentor_Transformer_Writer_Abstract
    extends phpDocumentor_Transformer_Abstract
{
    /**
     * Abstract definition of the execute method.
     *
     * @param DOMDocument                        $structure      Document
     *  containing the structure.
     * @param phpDocumentor_Transformer_Transformation $transformation Transformation
     *  to execute.
     *
     * @return void
     */
    abstract public function transform(
        DOMDocument $structure, phpDocumentor_Transformer_Transformation $transformation
    );

    /**
     * Returns an instance of a writer and caches it; a single writer
     * instance is capable of transforming multiple times.
     *
     * @param string $writer Name of thr writer to get.
     *
     * @return phpDocumentor_Transformer_Writer_Abstract
     */
    static public function getInstanceOf($writer)
    {
        static $writers = array();

        // first attempt to find out whether it is the name of a core
        // writer or a full writer name; the first is provided for aliases
        // and backwards compatibility
        if (!@class_exists($writer)) {
            $writer_class = 'phpDocumentor_Plugin_Core_Transformer_Writer_'
                . ucfirst($writer);
        } else {
            $writer_class = $writer;
        }

        if (!@class_exists($writer_class)) {
            throw new phpDocumentor_Transformer_Exception(
                'Unknown writer was mentioned in the transformation of a '
                . 'template: ' . $writer_class
            );
        }

        // if there is no writer in cache; create it
        if (!isset($writers[strtolower($writer_class)])) {
            $writers[strtolower($writer_class)] = new $writer_class();
        }

        return $writers[strtolower($writer_class)];
    }
}
