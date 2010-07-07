<?php

class DocBlox_Writer_Xslt extends DocBlox_Writer_Abstract
{
  function generateFilename($file)
  {
    $info = pathinfo(str_replace('/', '_', trim($file, '/.')));

    return $info['filename'].'.html';
  }

  function generateFiles($files, $xml)
  {
    // prepare the xsl document
    $xsl = new DOMDocument;
    $xsl->load('resources/file.xsl');

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules

    // process each file and store it in a separate .html file
    $files_path = realpath($this->target).'/files';
    if (!file_exists($files_path))
    {
      mkdir($files_path, 0755, true);
    }

    foreach($files as $file)
    {
      $proc->setParameter('', 'file', $file);
      $proc->setParameter('', 'title', $file);
      $proc->transformToURI($xml, 'file://'.$files_path.'/'.$this->generateFilename($file));
    }
  }

  public function execute()
  {
    $target_path = realpath($this->target);

    // copy all generic files to the target folder
    $this->copyRecursive('./resources/js', $target_path.'/js');
    $this->copyRecursive('./resources/css', $target_path.'/css');
    $this->copyRecursive('./resources/images', $target_path.'/images');

    // copy all theme files over the previously copied directories, this enables us to override generic files
    $theme_path = $this->theme_path.DIRECTORY_SEPARATOR.$this->theme;
    $this->copyRecursive($theme_path.'/js', $target_path.'/js');
    $this->copyRecursive($theme_path.'/css', $target_path.'/css');
    $this->copyRecursive($theme_path.'/images', $target_path.'/images');


    // Load the XML source
    $xml = new DOMDocument();
    $xml->load($target_path.'/structure.xml');

    // get a list of contained files
    $files = array();
    $xpath = new DOMXPath($xml);
    $qry = $xpath->query("/project/file[@path]");

    $files_path = $target_path.'/files';
    /** @var DOMElement $element */
    foreach ($qry as $element)
    {
      $files[] = $element->getAttribute('path');
      $element->setAttribute('generated-path', $files_path.'/'.$this->generateFilename($element->getAttribute('path')));
    }

    $this->generateFiles($files, $xml);

    $class_graph = new DocBlox_Writer_Xslt_ClassGraph();
    $class_graph->execute($xml);
  }
}
