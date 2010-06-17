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
    echo $dom->saveXML();
  }
}

echo 'Memory: '.memory_get_usage().PHP_EOL;

$nabu = new Nabu();

$file = $nabu->parseFile('test.php');
unset($file);
echo 'Memory: '.memory_get_usage().PHP_EOL;

//$file = $nabu->parseFile('Nabu/TokenIterator.php');
//unset($file);
//echo 'Memory: '.memory_get_usage().PHP_EOL;
//
//$file = $nabu->parseFile('Nabu/Token.php');
//unset($file);
//echo 'Memory: '.memory_get_usage().PHP_EOL;
//
//$file = $nabu->parseFile('Nabu/Abstract.php');
//unset($file);
//echo 'Memory: '.memory_get_usage().PHP_EOL;
//
//$file = $nabu->parseFile('Nabu/File.php');
//unset($file);
//echo 'Memory: '.memory_get_usage().PHP_EOL;
//
//if (file_exists('/home/mvriel/Projects/emma/1.0.14/plugins/emmaOrderManagementPlugin/lib/model/om/BaseAoipProductionOrder.php'))
//{
//  $file = $nabu->parseFile('/home/mvriel/Projects/emma/1.0.14/plugins/emmaOrderManagementPlugin/lib/model/om/BaseAoipProductionOrder.php');
//  unset($file);
//  echo 'Memory: '.memory_get_usage().PHP_EOL;
//}
//
//if (file_exists('/home/mvriel/Projects/emma/1.0.14/plugins/emmaOrderManagementPlugin/lib/model/om/BasePoipProductionOrder.php'))
//{
//  $file = $nabu->parseFile('/home/mvriel/Projects/emma/1.0.14/plugins/emmaOrderManagementPlugin/lib/model/om/BasePoipProductionOrder.php');
//  unset($file);
//  echo 'Memory: '.memory_get_usage().PHP_EOL;
//}

/*
if (file_exists('/home/mvriel/Projects/emma/1.0.14/plugins/emmaOrderManagementPlugin/lib/model/om/BaseKpnProductionOrder.php'))
{
  $file = $nabu->parseFile('/home/mvriel/Projects/emma/1.0.14/plugins/emmaOrderManagementPlugin/lib/model/om/BaseKpnProductionOrder.php');
  unset($file);
  echo 'Memory: '.memory_get_usage().PHP_EOL;
}
*/

//$file = $nabu->parseFile('test.php');
//
//$dom = new DOMDocument();
//$dom->loadXML($file->__toXml());
//$dom->formatOutput = true;
//echo $dom->saveXML();
$nabu->parseDirectory('.');