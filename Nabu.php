<?php
class Nabu extends Nabu_Abstract
{
  protected $ignore_patterns = array();

  public function addIgnorePattern($pattern)
  {
    $this->ignore_patterns[] = $patterns;
  }

  public function setIgnorePatterns(array $patterns)
  {
    $this->ignore_patterns = $patterns;
  }

  function parseFile($file)
  {
    $this->debug('Starting to parse file: '.$file);
    $this->resetTimer();
    try
    {
      $file = new Nabu_File($file);
      $file->process();
    } catch(Exception $e)
    {
      $this->log('  Unable to parse file, an error was detected: '.$e->getMessage());
      $this->debug('  Unable to parse file, an error was detected: '.$e->getMessage());
      $file = false;
    }
    $this->debug('  Used memory: '.memory_get_usage());
    $this->debugTimer('  Parsed file');

    return $file;
  }

  function parseFiles($files)
  {
    echo 'Starting to process '.count($files).' files'.PHP_EOL;
    $timer = new sfTimer();

    // convert patterns to regex's
    foreach($this->ignore_patterns as &$pattern)
    {
      $pattern = $this->convertToPregCompliant($pattern);
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML('<project></project>');
    foreach ($files as $file)
    {
      foreach($this->ignore_patterns as $pattern)
      {
        if (preg_match('/^'.$pattern.'$/', $file))
        {
          echo '  File "'.$file.'" matches ignore pattern, skipping'.PHP_EOL;
          continue 2;
        }
      }

      echo '  Parsing "'.$file.'" ... ';
      $timer_file = new sfTimer();

      $object = $this->parseFile($file);
      if ($object === false)
      {
        continue;
      }

      $dom_prop = new DOMDocument();
      $dom_prop->loadXML(trim($object->__toXml()));

      $xpath = new DOMXPath($dom_prop);
      $qry = $xpath->query('/*');
      for ($i = 0; $i < $qry->length; $i++)
      {
        $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
      }

      echo 'memory: '.memory_get_usage().', duration: '.round($timer_file->getElapsedTime(), 2).'s'.PHP_EOL;
    }

    $dom->formatOutput = true;
    $xml = $dom->saveXML();
    echo 'Elapsed time: '.round($timer->getElapsedTime(), 2).'s'.PHP_EOL;

    return $xml;
  }

  /**
   * Converts $s into a string that can be used with preg_match
   *
   * @param string $string string with wildcards ? and *
   *
   * @author Greg Beaver <cellog@php.net>
   *
   * @see PhpDocumentor/phpDocumentor/Io.php
   *
   * @return string converts * to .*, ? to ., etc.
   */
  function convertToPregCompliant($string)
  {
      $y = '\/';
      if (DIRECTORY_SEPARATOR == '\\')
      {
          $y = '\\\\';
      }
      $string = str_replace('/', DIRECTORY_SEPARATOR, $string);
      $x = strtr($string, array('?' => '.','*' => '.*','.' => '\\.','\\' => '\\\\','/' => '\\/',
                              '[' => '\\[',']' => '\\]','-' => '\\-'));
      if (strpos($string, DIRECTORY_SEPARATOR) !== false &&
          strrpos($string, DIRECTORY_SEPARATOR) === strlen($string) - 1)
      {
          $x = "(?:.*$y$x?.*|$x.*)";
      }
      return $x;
  }
}