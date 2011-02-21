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
  }

  public function getSource()
  {
    return $this->source;
  }

  public function setSource($source)
  {
    $this->source = $source;

    $xml = simplexml_load_file(realpath($this->getSource()));
    if ($xml['version'] != DocBlox_Abstract::VERSION)
    {
      throw new Exception('Version of transformer ('. DocBlox_Abstract::VERSION.') did not match the '
        . 'structure file (' . $xml['version'] . '); please re-parse the project first');
    }
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

  /**
   * Returns the path to the current theme.
   *
   * @return string
   */
  public function getThemePath()
  {
    return $this->theme_path.DIRECTORY_SEPARATOR.$this->getTheme();
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

  /**
   * Returns the relative root path of given file's directory path.
   *
   * @param string $destination_file The path of the file whose root you want
   *
   * @return string
   */
  protected function getRelativeRoot($destination_file)
  {
    $offset = substr_count($destination_file, '../') * 2;
    return str_repeat('../', substr_count($destination_file, '/') + 1 - $offset);
  }

  /**
   * Returns the path of the target folder where the transformed 'source file' HTML files should be stored.
   *
   * If the path does not exist this function tries to create it.
   *
   * @return string
   */
  protected function getTargetFilesPath()
  {
    $files_path = $this->getTarget() . '/files';
    if (!file_exists($files_path))
    {
      $this->log('  Add "files" directory');
      mkdir($files_path, 0755, true);
    }

    return $files_path;
  }

  /**
   * Uses an existing XSLProcessor (thus template) to transfor the given $xml to a file at location $destination_file.
   *
   * @param DOMDocument   $xml
   * @param XSLTProcessor $proc
   * @param string        $destination_file
   *
   * @return void
   */
  protected function transformTemplateToFile(DOMDocument $xml, XSLTProcessor $proc, $destination_file)
  {
    $files_path = $this->getTargetFilesPath();
    $root       = $this->getRelativeRoot($destination_file);
    $proc->setParameter('', 'search_template', ($this->getSearchObject() !== false) ? $this->getSearchObject()->getXslTemplateName() : 'none');

    // root differs, since most cached files rely on the root we will have to repopulate
    if ($proc->getParameter('', 'root') !== substr($root ? $root : './', 0, -1))
    {
      $proc->setParameter('', 'root', substr($root ? $root : './', 0, -1));
      if (file_exists($this->getThemePath().'/preprocess'))
      {
        $dirs = new DirectoryIterator($this->getThemePath().'/preprocess');

        /** @var DirectoryIterator $file */
        foreach($dirs as $file)
        {
          if (!$file->isFile())
          {
            continue;
          }

          $this->log('  Preprocessing '.$file->getFilename() . ' as XSLT parameter $'.$file->getBasename('.xsl'));
          $xsl2 = new DOMDocument();
          $xsl2->load($this->getThemePath() . '/preprocess/' . $file->getFilename());

          $proc2 = new XSLTProcessor();
          $proc2->importStyleSheet($xsl2);
          $proc2->setParameter('', 'root', substr($root ? $root : './', 0, -1));

          $proc->setParameter('', $file->getBasename('.xsl'), str_replace('\'', '&quot;', $proc2->transformToXml($xml)));
        }
      }
    }

    $proc->transformToURI($xml, 'file://' . $files_path . '/' . $destination_file);
  }

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
