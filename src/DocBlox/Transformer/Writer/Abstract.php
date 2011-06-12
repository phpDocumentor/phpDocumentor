<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Writers
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Base class for the actual transformation business logic (writers).
 *
 * @category   DocBlox
 * @package    Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
abstract class DocBlox_Transformer_Writer_Abstract extends DocBlox_Core_Abstract
{
  /**
   * Abstract definition of the execute method.
   *
   * @param DOMDocument                        $structure      Document containing the structure.
   * @param DocBlox_Transformer_Transformation $transformation Transformation to execute.
   *
   * @return void
   */
  abstract public function transform(DOMDocument $structure, DocBlox_Transformer_Transformation $transformation);

  /**
   * Returns an instance of a writer and caches it; a single writer instance is capable of transforming multiple times.
   *
   * @param string $writer Name of thr writer to get.
   *
   * @return DocBlox_Transformer_Writer_Abstract
   */
  static public function getInstanceOf($writer)
  {
    static $writers = array();

    // if there is no writer; create it
    if (!isset($writers[strtolower($writer)]))
    {
      $writer_class = 'DocBlox_Transformer_Writer_' . ucfirst($writer);
      $writers[strtolower($writer)] = new $writer_class();
    }

    return $writers[strtolower($writer)];
  }
}
