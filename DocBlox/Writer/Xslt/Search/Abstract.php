<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Xslt
 * @subpackage Search
 * @copyright  Copyright (c) 2010-2010 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Basic class for Search generators with the XSLT writer..
 *
 * @category   DocBlox
 * @package    Xslt
 * @subpackage Search
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
abstract class DocBlox_Writer_Xslt_Search_Abstract extends DocBlox_Abstract
{
  protected $target = '';

  /**
   * The target location for the search index.
   *
   * @param string $target
   *
   * @return void
   */
  public function __construct($target)
  {
    $this->target = $target;
  }

  abstract public function getXslTemplateName();

  abstract public function generateIndex($xml);
}