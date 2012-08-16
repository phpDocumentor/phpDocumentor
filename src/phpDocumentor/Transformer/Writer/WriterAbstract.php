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

namespace phpDocumentor\Transformer\Writer;

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
abstract class WriterAbstract extends \phpDocumentor\Transformer\TransformerAbstract
{
    /**
     * Abstract definition of the execute method.
     *
     * @param \DOMDocument                        $structure      Document
     *  containing the structure.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation
     *  to execute.
     *
     * @return void
     */
    abstract public function transform(
        \DOMDocument $structure, \phpDocumentor\Transformer\Transformation $transformation
    );

    /**
     * Returns an instance of a writer and caches it; a single writer
     * instance is capable of transforming multiple times.
     *
     * @param string $writer Name of thr writer to get.
     *
     * @return \phpDocumentor\Transformer\Writer\WriterAbstract
     */
    static public function getInstanceOf($writer)
    {
        static $writers = array();
        $writer_class = 'phpDocumentor\Plugin\Core\Transformer\Writer\\'
            . ucfirst($writer);

        if (!self::isValidWriterClassname($writer_class)) {
            $writer_class = $writer;

            if (!self::isValidWriterClassname($writer_class)) {
                throw new \phpDocumentor\Transformer\Exception(
                    'Unknown writer was mentioned in the transformation of a '
                    . 'template: ' . $writer_class
                );
            }
        }

        // if there is no writer in cache; create it
        if (!isset($writers[strtolower($writer_class)])) {
            $writers[strtolower($writer_class)] = new $writer_class();
        }

        return $writers[strtolower($writer_class)];
    }

    /**
     * Checks whether the given classname is valid for use as writer.
     *
     * @param string $class_name Class name of the writer to check.
     *
     * @return bool
     */
    private static function isValidWriterClassname($class_name)
    {
        return class_exists($class_name)
            && is_subclass_of(
                $class_name, '\phpDocumentor\Transformer\Writer\WriterAbstract'
            );
    }
}
