<?php

class DocBlox_Writer_Xslt extends DocBlox_Writer_Xslt_Abstract
{

  protected function generateFilename($file)
  {
    $info = pathinfo(str_replace('/', '_', trim($file, '/.')));

    return $info['filename'].'.html';
  }

  protected function generateFiles($files, $xml)
  {
    $this->log('Started generating the static HTML files');

    // prepare the xsl document
    $xsl = new DOMDocument;
    $xsl->load($this->resource_path.'/file.xsl');
    $target = realpath($this->target);

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules
    $proc->setParameter('', 'root', '.');
    $proc->setParameter(
      '',
      'search_template',
      ($this->getSearchObject() !== false)
        ? $this->getSearchObject()->getXslTemplateName()
        : 'none'
    );

    // process each file and store it in a separate .html file
    $files_path = $target.'/files';
    if (!file_exists($files_path))
    {
      mkdir($files_path, 0755, true);
    }

    $file_count = count($files);
    foreach($files as $key => $file)
    {
      $this->log('Processing file #'.str_pad(($key+1), strlen($file_count), ' ', STR_PAD_LEFT).' of '.$file_count.': '.$file);
      $proc->setParameter('', 'file', $file);
      $proc->setParameter('', 'title', $file);
      $file_name = $this->generateFilename($file);
      $root = str_repeat('../', substr_count($file_name, '/')+1);
      $proc->setParameter('', 'root', substr($root ? $root : './', 0, -1));
      $proc->transformToURI($xml, 'file://'.$files_path.'/'.$file_name);
    }

    $this->log('Finished generating the static HTML files');

    $this->log('Processing markers');
    $xsl = new DOMDocument;
    $xsl->load($this->resource_path.'/markers.xsl');

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules
    $proc->setParameter('', 'root', '.');
    $proc->setParameter('', 'title', 'Markers');
    $proc->setParameter(
      '',
      'search_template',
      ($this->getSearchObject() !== false)
        ? $this->getSearchObject()->getXslTemplateName()
        : 'none'
    );
    $proc->transformToURI($xml, 'file://'.$target.'/markers.html');
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
    return 'files/'.$this->generateFilename($path);
  }

  public function execute()
  {
    $target_path = realpath($this->getTarget());
    $source_file = realpath($this->getSource());

    // copy all generic files to the target folder
    copy($this->resource_path.'/ajax_search.php', $target_path.'/ajax_search.php');
    $this->copyRecursive($this->resource_path.'/js', $target_path.'/js');
    $this->copyRecursive($this->resource_path.'/css', $target_path.'/css');
    $this->copyRecursive($this->resource_path.'/images', $target_path.'/images');

    // copy all theme files over the previously copied directories, this enables us to override generic files
    $theme_path = $this->theme_path.DIRECTORY_SEPARATOR.$this->theme;
    $this->copyRecursive($theme_path.'/js', $target_path.'/js');
    $this->copyRecursive($theme_path.'/css', $target_path.'/css');
    $this->copyRecursive($theme_path.'/images', $target_path.'/images');

    // Load the XML source
    $xml = new DOMDocument();
    $xml->load($source_file);

    // get a list of contained files
    $files = array();
    $xpath = new DOMXPath($xml);

    // find all files and add a generated-path variable
    $qry = $xpath->query("/project/file[@path]");
    foreach ($qry as $element)
    {
      $files[] = $element->getAttribute('path');
      $element->setAttribute('generated-path', $this->generateFullPath($element->getAttribute('path')));
    }

    // add extra xml elements to tags
    $qry = $xpath->query('//docblock/tag');
    /** @var DOMElement $element */
    foreach ($qry as $element)
    {
      // if a tag has a type, add a link to the given file if it exists in the xml
      if ($element->hasAttribute('type'))
      {
        // find the path of the file of the class referred by the type (namespaces are taken into account (full_name))
        $query = '//class[full_name=\''.$element->getAttribute('type').'\']/../@path';

        /** @var DOMNodeList $res */
        $res = $xpath->query($query);
        if ($res->length > 0)
        {
          // if a path was found, convert it to a filename and add it onto the element
          $file_name = $this->generateFilename($res->item(0)->nodeValue);
          $element->setAttribute('link', $file_name);
        }
      }

      // add a 15 character excerpt of the node contents, meant for the sidebar
      $element->setAttribute('excerpt', utf8_encode(substr($element->nodeValue, 0, 15).(strlen($element->nodeValue) > 15 ? '...' : '')));
    }

    $this->generateFiles($files, $xml);
    if ($this->getSearchObject() !== false)
    {
      $this->getSearchObject()->generateIndex($xml);
    }

    $this->log('Generating the class diagram');
    $class_graph = new DocBlox_Writer_Xslt_ClassGraph();
    $class_graph->setTarget($this->getTarget());
    $class_graph->setSearchObject($this->getSearchObject());
    $class_graph->execute($xml);
  }

}