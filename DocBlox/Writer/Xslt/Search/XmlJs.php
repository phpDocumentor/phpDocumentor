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
 * Search generator for pure inline javascript search using an external XML file.
 *
 * Warning: this search method only works for smaller projects, with larger projects the index file becomes too large.
 *
 * @category   DocBlox
 * @package    Xslt
 * @subpackage Search
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Writer_Xslt_Search_XmlJs extends DocBlox_Writer_Xslt_Search_Abstract
{
  /**
   * Identifies the XslTemplate to use for inserting Javascript.
   *
   * @return string
   */
  public function getXslTemplateName()
  {
    return 'xmljs';
  }

  /**
   * Generates the search index file and saves it to the target location.
   *
   * @param SimpleXMLElement $xml
   *
   * @return void
   */
  public function generateIndex($xml)
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
}