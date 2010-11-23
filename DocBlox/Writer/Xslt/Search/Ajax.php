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
 * Search generator for search scripts which invoke the PHP ajax script.
 *
 * This only works when the site is being loaded through an apache webserver. The PHP script
 * uses the same data generation as the parent XmlJs method. The generated XML file is loaded
 * and xpath is used to search through the file and return a JSON array.
 *
 * @category   DocBlox
 * @package    Xslt
 * @subpackage Search
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Writer_Xslt_Search_Ajax extends DocBlox_Writer_Xslt_Search_XmlJs
{
  /**
   * Identifies the XslTemplate to use for inserting Javascript.
   *
   * @return string
   */
  public function getXslTemplateName()
  {
    return 'ajax';
  }

}