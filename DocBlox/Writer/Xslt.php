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
    $this->log('Started generating the static HTML files');

    // prepare the xsl document
    $xsl = new DOMDocument;
    $xsl->load($this->resource_path.'/file.xsl');
    $target = realpath($this->target);

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules
    $proc->setParameter('', 'root', $target);

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
      $proc->transformToURI($xml, 'file://'.$files_path.'/'.$this->generateFilename($file));
    }

    $this->log('Finished generating the static HTML files');

    $this->log('Processing markers');
    $xsl = new DOMDocument;
    $xsl->load($this->resource_path.'/markers.xsl');

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules
    $proc->setParameter('', 'root', $target);
    $proc->setParameter('', 'title', 'Markers');
    $proc->transformToURI($xml, 'file://'.$target.'/markers.html');
  }

  protected function generateSearchIndex($xml)
  {
    $this->log('Generating the search index');

    $output = new SimpleXMLElement('<nodes></nodes>');
    $xml    = simplexml_import_dom($xml);

    foreach ($xml->file as $file)
    {
      foreach($file->interface as $interface)
      {
        $interface_node        = $output->addChild('node');
        $interface_node->value = (string)$interface->name;
        $interface_node->id    = $file['generated-path'].'#'.$interface_node->value;
        $interface_node->type  = 'interface';

        foreach ($interface->constant as $constant)
        {
          $node        = $output->addChild('node');
          $js_path     = (string)$interface->name.'/constants_'.(string)$interface->name.'/';
          $node->value = (string)$interface->name.'::'.(string)$interface->name;
          $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
          $node->type  = 'constant';
        }
        foreach ($interface->property as $property)
        {
          $node        = $output->addChild('node');
          $js_path     = (string)$interface->name.'/properties_'.(string)$interface->name.'/';
          $node->value = (string)$interface->name.'::'.(string)$property->name;
          $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
          $node->type  = 'property';
        }
        foreach ($interface->method as $method)
        {
          $node        = $output->addChild('node');
          $js_path     = (string)$interface->name.'/methods_'.(string)$interface->name.'/';
          $node->value = (string)$interface->name.'::'.(string)$method->name.'()';
          $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
          $node->type  = 'method';
        }
      }

      foreach($file->class as $class)
      {
        $class_node        = $output->addChild('node');
        $class_node->value = (string)$class->name;
        $class_node->id    = $file['generated-path'].'#'.$class_node->value;
        $class_node->type  = 'class';

        foreach ($class->constant as $constant)
        {
          $node        = $output->addChild('node');
          $js_path     = (string)$class->name.'/constants_'.(string)$class->name.'/';
          $node->value = (string)$class->name.'::'.(string)$constant->name;
          $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
          $node->type  = 'constant';
        }
        foreach ($class->property as $property)
        {
          $node        = $output->addChild('node');
          $js_path     = (string)$class->name.'/properties_'.(string)$class->name.'/';
          $node->value = (string)$class->name.'::'.(string)$property->name;
          $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
          $node->type  = 'property';
        }
        foreach ($class->method as $method)
        {
          $node        = $output->addChild('node');
          $js_path     = (string)$class->name.'/methods_'.(string)$class->name.'/';
          $node->value = (string)$class->name.'::'.(string)$method->name.'()';
          $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
          $node->type  = 'method';
        }
      }

      foreach ($file->constant as $constant)
      {
        $node        = $output->addChild('node');
        $js_path     = 'file_constants/';
        $node->value = (string)$constant->name;
        $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
        $node->type  = 'constant';
      }
      foreach ($file->function as $function)
      {
        $node        = $output->addChild('node');
        $js_path     = 'file_functions/';
        $node->value = (string)$function->name.'()';
        $node->id    = $file['generated-path'].'#'.$js_path.$node->value;
        $node->type  = 'function';
      }
    }

    $target_path = realpath($this->target);
    $output->asXML($target_path.'/search_index.xml');
  }

  function generateFullPath($path)
  {
    $target_path = realpath($this->target);
    $files_path = $target_path.'/files';

    return $files_path.'/'.$this->generateFilename($path);
  }

  public function execute()
  {
    $target_path = realpath($this->getTarget());
    $source_file = realpath($this->getSource());

    // copy all generic files to the target folder
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
    $qry = $xpath->query("/project/file[@path]");

    /** @var DOMElement $element */
    foreach ($qry as $element)
    {
      $files[] = $element->getAttribute('path');
      $element->setAttribute('generated-path', $this->generateFullPath($element->getAttribute('path')));
    }

    $this->generateFiles($files, $xml);
    $this->generateSearchIndex($xml);

    $this->log('Generating the class diagram');
    $class_graph = new DocBlox_Writer_Xslt_ClassGraph();
    $class_graph->setTarget($this->getTarget());
    $class_graph->execute($xml);
  }
}
