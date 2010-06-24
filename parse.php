<?php
require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Nabu_');

class Nabu extends Nabu_Abstract
{
  function parseFile($file)
  {
    $this->debug('Starting to parse file: '.$file);
    $time = microtime(true);
    $file = new Nabu_File($file);
    $this->debug('  Used memory: '.memory_get_usage());
    $elapsed = microtime(true) - $time;
    $this->debug('  Elapsed time: '.$elapsed.'s');

    return $file;
  }

  function parseDirectory($path)
  {
    $files = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
    $processing_files = array();

    /** @var SplFileInfo $file */
    foreach(new RecursiveIteratorIterator($files) as $file)
    {
      $extension = pathinfo($file->getPathname(), PATHINFO_EXTENSION);
      if (strtolower($extension) != 'php')
      {
        continue;
      }

      $processing_files[] = $file->getPathname();
    }

    echo 'Starting to process '.count($processing_files).' files';
    $time = microtime(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML('<project></project>');
    foreach ($processing_files as $file)
    {
      echo '  Parsing "'.$file.'" ... ';
      $object = $this->parseFile($file);

      $dom_prop = new DOMDocument();
      $dom_prop->loadXML(trim($object->__toXml()));

      $xpath = new DOMXPath($dom_prop);
      $qry = $xpath->query('/*');
      for ($i = 0; $i < $qry->length; $i++)
      {
        $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
      }

      echo 'memory after processing: '.memory_get_usage().PHP_EOL;
    }

    $dom->formatOutput = true;
    $xml = $dom->saveXML();
    $elapsed = microtime(true) - $time;
    echo 'Elapsed time: '.round($elapsed, 2).'s';

    return $xml;
  }

  protected function processGenericInformation(Nabu_TokenIterator $tokens)
  {
  }

  public function __toXml()
  {
    return false;
  }

}

echo 'Memory: '.memory_get_usage().PHP_EOL;

$nabu = new Nabu();

//$file = $nabu->parseFile('../../Projects/emma.unet.nl/plugins/emmaProductManagementPlugin/lib/model/om/BaseFeature.php');
$file = $nabu->parseFile('./test.php');

$dom = new DOMDocument('1.0', 'UTF-8');
$dom->loadXML('<project></project>');

$dom_prop = new DOMDocument();
$dom_prop->loadXML(trim($file->__toXml()));

$xpath = new DOMXPath($dom_prop);
$qry = $xpath->query('/*');
for ($i = 0; $i < $qry->length; $i++)
{
  $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
}
$dom->formatOutput = true;

file_put_contents('structure.xml', $dom->saveXML());

//file_put_contents('structure.xml', $nabu->parseDirectory('.'));
//file_put_contents('structure.xml', $nabu->parseDirectory('../../Projects/emma.unet.nl/plugins/emmaProductManagementPlugin/lib/model/'));
