<?php
/**
 * @author    mvriel
 * @copyright
 */

/**
 * Provide a short description for this class.
 *
 * @author     mvriel
 * @package
 * @subpackage
 */
class DocBlox_Writer_Abstract extends DocBlox_Abstract
{
  protected $theme         = 'default';
  protected $theme_path    = '';
  protected $target        = '';
  protected $source        = '';
  protected $resource_path = '';

  public function __construct()
  {
    $root_path           = realpath(dirname(__FILE__).'/../..');
    $this->resource_path = $root_path.'/resources';
    $this->theme_path    = $this->resource_path.'/themes';
    $this->target        = $root_path.'/output';
    $this->source        = $root_path.'/output/structure.xml';
  }

  public function getSource()
  {
    return $this->source;
  }

  public function setSource($source)
  {
    $this->source = $source;
  }

  public function getTarget()
  {
    return $this->target;
  }

  public function setTarget($target)
  {
    $this->target = $target;
  }

  public function getTheme()
  {
    return $this->theme;
  }

  public function setTheme($theme)
  {
    $this->theme = $theme;
  }

  function copyRecursive($src, $dst)
  {
    $dir = opendir($src);
    if (!$dir)
    {
      throw new Exception('Unable to locate path "'.$src.'"');
    }

    // check if the folder exists, otherwise create it
    if ((!file_exists($dst)) && (false === mkdir($dst)))
    {
      throw new Exception('Unable to create folder "'.$dst.'"');
    }

    while (false !== ($file = readdir($dir)))
    {
      if (($file != '.') && ($file != '..'))
      {
        if (is_dir($src . '/' . $file))
        {
          $this->copyRecursive($src . '/' . $file, $dst . '/' . $file);
        }
        else
        {
          copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

}
