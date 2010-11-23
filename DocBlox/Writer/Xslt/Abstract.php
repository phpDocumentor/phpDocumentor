<?php
/**
 * Provide a short description for this class.
 *
 * @package    emma
 * @subpackage
 * @author     mvriel
 */
class DocBlox_Writer_Xslt_Abstract extends DocBlox_Writer_Abstract
{
  protected $search = null;

  /**
   * Returns the search object used in the generation of these templates.
   *
   * @return DocBlox_Writer_Xslt_Search_Abstract
   */
  public function getSearchObject()
  {
    if ($this->search === null)
    {
      $this->setSearchObject('XmlJs');
    }

    return $this->search;
  }

  /**
   * Sets the type of search and initializes a Search object.
   *
   * @param string $type
   *
   * @return void
   */
  public function setSearchObject($type)
  {
    if (is_string($type))
    {
      if (strtolower($type) == "none")
      {
        $this->search = false;
        return;
      }

      $class_name = 'DocBlox_Writer_Xslt_Search_'.$type;
      if (!class_exists($class_name))
      {
        throw new Exception('Search type "'.$type.'" does not exist');
      }
      $this->search = new $class_name($this->getTarget());
      return;
    }

    $this->search = $type;
  }
}