<?php

class DocBlox_Writer_Xslt extends DocBlox_Writer_Xslt_Abstract
{
  /**
   * Converts a source file name to the name used for generating the end result.
   *
   * @param string $file
   *
   * @return string
   */
  protected function generateFilename($file)
  {
    $info = pathinfo(str_replace(DIRECTORY_SEPARATOR, '_', trim($file, DIRECTORY_SEPARATOR.'.')));
    return $info['filename'] . '.html';
  }

  /**
   * Generates the markers file (TODO, FIXME, etc).
   *
   * @param DOMDocument $xml
   *
   * @return void
   */
  protected function generateMarkers($xml)
  {
    $this->log('Processing markers');
    $xsl  = new DOMDocument();
    $proc = new XSLTProcessor();
    $xsl->load($this->getThemePath() . '/markers.xsl');
    $proc->importStyleSheet($xsl);
    $proc->setParameter('', 'title', 'Markers');
    $this->transformTemplateToFile($xml, $proc, '../markers.html');
  }

  /**
   * Generate the documentation pages for the given files.
   *
   * @param string[] $files
   * @param DOMDocument $xml
   *
   * @return void
   */
  protected function generateFiles($files, $xml)
  {
    $this->log('Started generating the static HTML files');

    // prepare the xsl document
    $xsl = new DOMDocument();
    $xsl->load($this->getThemePath() . '/file.xsl');

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules
    $proc->setParameter('', 'search_template', ($this->getSearchObject() !== false) ? $this->getSearchObject()->getXslTemplateName() : 'none');

    $file_count = count($files);
    foreach ($files as $key => $file)
    {
      $this->log('Processing file #' . str_pad(($key + 1), strlen($file_count), ' ', STR_PAD_LEFT) . ' of ' . $file_count . ': ' . $file);
      $proc->setParameter('', 'file', $file);
      $proc->setParameter('', 'title', $file);
      $this->transformTemplateToFile($xml, $proc, $this->generateFilename($file));
    }
    $this->log('Finished generating the static HTML files');
  }

  /**
   * Returns _the_ path used to identify file locations.
   *
   * @param string $path
   *
   * @return string
   */
  protected function generateFullPath($path)
  {
    return 'files/' . $this->generateFilename($path);
  }

  public function execute()
  {
    $this->log('Starting transformation');

    $target_path = realpath($this->getTarget());
    $source_file = realpath($this->getSource());

    // copy all generic files to the target folder
    $this->log('Copying layout files');
    copy($this->resource_path . '/ajax_search.php', $target_path . '/ajax_search.php');
    $this->copyRecursive($this->resource_path . '/js', $target_path . '/js');
    $this->copyRecursive($this->resource_path . '/css', $target_path . '/css');
    $this->copyRecursive($this->resource_path . '/images', $target_path . '/images');

    // copy all theme files over the previously copied directories, this enables us to override generic files
    $this->log('Copying theme');
    $this->copyRecursive($this->getThemePath() . '/js', $target_path . '/js');
    $this->copyRecursive($this->getThemePath() . '/css', $target_path . '/css');
    $this->copyRecursive($this->getThemePath() . '/images', $target_path . '/images');

    // Load the XML source
    $xml = new DOMDocument();
    $xml->load($source_file);

    // get a list of contained files
    $files = array();
    $xpath = new DOMXPath($xml);

    // find all files and add a generated-path variable
    $this->log('Adding path information to each xml "file" tag');
    $qry = $xpath->query("/project/file[@path]");
    foreach ($qry as $element)
    {
      $files[] = $element->getAttribute('path');
      $element->setAttribute('generated-path', $this->generateFullPath($element->getAttribute('path')));
    }

    $qry = $xpath->query('//class[full_name]/..');
    $class_paths = array();

    /** @var DOMElement $element */
    foreach ($qry as $element)
    {
      $path = $element->getAttribute('path');
      foreach ($element->getElementsByTagName('class') as $class)
      {
        $class_paths[$class->getElementsByTagName('full_name')->item(0)->nodeValue] = $path;
      }
    }

    // add extra xml elements to tags
    $this->log('Adding link information and excerpts to all DocBlock tags');
    $qry = $xpath->query('//docblock/tag');

    /** @var DOMElement $element */
    foreach ($qry as $element)
    {
      if ($element->getAttribute('name') == 'see')
      {
        $node_value = explode('::', $element->nodeValue);
        if (isset($class_paths[$node_value[0]]))
        {
          $file_name = $this->generateFilename($class_paths[$node_value[0]]);
          $element->setAttribute('link', $file_name.'#'.$element->nodeValue);
        }
      }

      // if a tag has a type, add a link to the given file if it exists in the xml
      if ($element->hasAttribute('type'))
      {
        // if a path was found, convert it to a filename and add it onto the element
        if (isset($class_paths[$element->getAttribute('type')]))
        {
          $file_name = $this->generateFilename($class_paths[$element->getAttribute('type')]);
          $element->setAttribute('link', $file_name);
        }
      }

      // add a 15 character excerpt of the node contents, meant for the sidebar
      $element->setAttribute('excerpt', utf8_encode(substr($element->nodeValue, 0, 15) . (strlen($element->nodeValue) > 15 ? '...' : '')));
    }

    $this->generateFiles($files, $xml);
    $this->generateMarkers($xml);
    if ($this->getSearchObject() !== false)
    {
      $this->getSearchObject()->generateIndex($xml);
    }

    $this->log('Generating the class diagram');
    $class_graph = new DocBlox_Writer_Xslt_ClassGraph();
    $class_graph->setTarget($this->getTarget());
    $class_graph->setTheme($this->getTheme());
    $class_graph->setSearchObject($this->getSearchObject());
    $class_graph->execute($xml);
  }
}