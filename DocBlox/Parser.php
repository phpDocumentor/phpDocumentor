<?php
class DocBlox_Parser extends DocBlox_Abstract
{
  protected $ignore_patterns = array();
  protected $existing_xml = null;

  /**
   * Imports an existing XML source to enable incremental parsing
   *
   * @param string $xml
   *
   * @return void
   */
  public function setExistingXml($xml)
  {
    $dom = new DOMDocument();
    $dom->loadXML($xml);

    $this->existing_xml = $dom;
  }

  public function addIgnorePattern($pattern)
  {
    $this->ignore_patterns[] = $pattern;
  }

  public function setIgnorePatterns(array $patterns)
  {
    $this->ignore_patterns = $patterns;
  }

  function parseFile($filename)
  {
    $this->debug('Starting to parse file: '.$filename);
    $this->resetTimer();
    $result = null;

    try
    {
      $file = new DocBlox_Reflection_File($filename);

      if ($this->existing_xml !== null)
      {
        $xpath = new DOMXPath($this->existing_xml);

        /** @var DOMNodeList $qry */
        $qry = $xpath->query('/project/file[@path=\''.$filename.'\' and @hash=\''.$file->getHash().'\']');
        if ($qry->length > 0)
        {
          $new_dom = new DOMDocument;
          $new_dom->appendChild($new_dom->importNode($qry->item(0), true));
          $result = $new_dom->saveXML();

          $this->log('  File "'.$filename.'" has not changed since last build, reusing the old definition');
        }
      }

      if ($result === null)
      {
        $file->process();
        $result = $file->__toXml();
      }
    } catch(Exception $e)
    {
      $this->log('  Unable to parse file, an error was detected: '.$e->getMessage());
      $this->debug('  Unable to parse file, an error was detected: '.$e->getMessage());
      $result = false;
    }
    $this->debug('  Used memory: '.memory_get_usage());
    $this->debugTimer('  Parsed file');

    return $result;
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

      $xml = $this->parseFile($file);
      if ($xml === false)
      {
        continue;
      }

      $dom_prop = new DOMDocument();
      $dom_prop->loadXML(trim($xml));

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