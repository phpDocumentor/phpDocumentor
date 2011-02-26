<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Writer
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Base class for the actual transformation business logic (writers).
 *
 * @category   DocBlox
 * @package    Writer
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
abstract class DocBlox_Writer_Abstract extends DocBlox_Abstract
{
  /**
   * Abstract definition of the execute method.
   *
   * @param string $query
   * @param string $source
   * @param string $artifact
   *
   * @return void
   */
  abstract public function transform(DOMDocument $structure, DocBlox_Transformation $transformation);

  /**
   * Returns an instance of a writer and caches it; a single writer instance is capable of transforming multiple times.
   *
   * @param string $writer Name of thr writer to get.
   *
   * @return DocBlox_Writer_Abstract
   */
  static public function getInstanceOf($writer)
  {
    static $writers = array();

    // if there is no writer; create it
    if (!isset($writers[strtolower($writer)]))
    {
      $writer_class = 'DocBlox_Writer_' . ucfirst($writer);
      $writers[strtolower($writer)] = new $writer_class();
    }

    return $writers[strtolower($writer)];
  }
}
