<?php
function recurse_copy($src, $dst)
{
  $dir = opendir($src);
  @mkdir($dst);
  while (false !== ($file = readdir($dir)))
  {
    if (($file != '.') && ($file != '..'))
    {
      if (is_dir($src . '/' . $file))
      {
        recurse_copy($src . '/' . $file, $dst . '/' . $file);
      }
      else
      {
        copy($src . '/' . $file, $dst . '/' . $file);
      }
    }
  }
  closedir($dir);
}

recurse_copy('./resources/js', './output/js');
recurse_copy('./resources/css', './output/css');

// Load the XML source
$xml = new DOMDocument();
$xml->load('structure.xml');
$el = $xml->createElement('header', file_get_contents('templates/header.html'));
$xml->documentElement->appendChild($el);

// get a list of contained files
$files = array();
$xpath = new DOMXPath($xml);
$qry = $xpath->query("/project/file[@path]");

/** @var DOMElement $element */
foreach ($qry as $element)
{
  $files[] = $element->getAttribute('path');
}

// prepare the xsl document
$xsl = new DOMDocument;
$xsl->load('templates/file.xsl');

// configure the transformer
$proc = new XSLTProcessor();
$proc->importStyleSheet($xsl); // attach the xsl rules

// process each file and store it in a separate .html file
$files_path = realpath('./output').'/files';
if (!file_exists($files_path))
{
  mkdir($files_path, 0755, true);
}
foreach($files as $file)
{
  echo 'Processing file: '.$file.PHP_EOL;
  $proc->setParameter('', 'file', $file);
  $info = pathinfo(str_replace('/', '_', trim($file, '/.')));
  $proc->transformToURI($xml, 'file://'.$files_path.'/'.$info['filename'].'.html');
}