<?php
class DocBlox_Parser extends DocBlox_Abstract
{
  protected $ignore_patterns = array();
  protected $existing_xml    = null;
  protected $force           = false;
  protected $validate        = false;
  protected $markers         = array('TODO', 'FIXME');
  protected $path            = null;

  public function isForced()
  {
    return $this->force;
  }

  public function doValidation()
  {
    return $this->validate;
  }

  public function setForced($forced)
  {
    $this->force = $forced;
  }

  public function setValidate($validate)
  {
    $this->validate = $validate;
  }

  public function getMarkers()
  {
    return $this->markers;
  }

  public function setMarkers($markers)
  {
    $this->markers = $markers;
  }

  /**
   * Imports an existing XML source to enable incremental parsing
   *
   * @param string $xml
   *
   * @return void
   */
  public function setExistingXml($xml)
  {
    $dom = null;
    if ($xml !== null)
    {
      $dom = new DOMDocument();
      $dom->loadXML($xml);
    }

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
    $this->log('Starting to parse file: '.$filename);
    $this->debug('Starting to parse file: '.$filename);
    $this->resetTimer();
    $result = null;

    try
    {
      $file = new DocBlox_Reflection_File($filename, $this->doValidation());
      $file->setMarkers($this->getMarkers());
      $file->setRelativeFilename($this->getRelativeFilename($filename));
      if (($this->existing_xml !== null) && (!$this->isForced()))
      {
        $xpath = new DOMXPath($this->existing_xml);

        /** @var DOMNodeList $qry */
        $qry = $xpath->query('/project/file[@path=\''.ltrim($file->getName()  , './').'\' and @hash=\''.$file->getHash().'\']');
        if ($qry->length > 0)
        {
          $new_dom = new DOMDocument('1.0', 'utf-8');
          $new_dom->appendChild($new_dom->importNode($qry->item(0), true));
          $result = $new_dom->saveXML();

          $this->log('>> File has not changed since last build, reusing the old definition');
        }
      }

      if ($result === null)
      {
        $file->process();
        $result = $file->__toXml();
      }
    } catch(Exception $e)
    {
      $this->log('>>  Unable to parse file, an error was detected: '.$e->getMessage(), Zend_Log::ALERT);
      $this->debug('Unable to parse file "'.$filename.'", an error was detected: '.$e->getMessage());
      $result = false;
    }
    $this->debug('>> Memory after processing of file: '.number_format(memory_get_usage()).' bytes');
    $this->debugTimer('>> Parsed file');

    return $result;
  }

  /**
   * Sets the path to the files that will be parsed.
   *
   * @param string $path
   */
  function setPath($path)
  {
	  $this->path=$path;
  }

  /**
   * Returns the filename, relative to the root of the source-directory.
   *
   * @param string $filename
   *
   * @return string
   */
  protected function getRelativeFilename($filename)
  {
	  $filename=preg_replace('~^'.preg_quote($this->path).'~','',$filename);
	  return ltrim($filename,'/');
  }

  /**
   * Generates a hierarchical array of namespaces with their singular name from a single level list of namespaces
   * with their full name.
   *
   * @param array $namespaces
   *
   * @return array
   */
  protected function generateNamespaceTree($namespaces)
  {
    sort($namespaces);

    $result = array();
    foreach ($namespaces as $namespace)
    {
      $namespace_list = explode('\\', $namespace);

      $node = &$result;
      foreach($namespace_list as $singular)
      {
        if (!isset($node[$singular]))
        {
          $node[$singular] = array();
        }

        $node = &$node[$singular];
      }
    }

    return $result;
  }

  /**
   * Recursive method to create a hierarchical set of nodes in the dom.
   *
   * @param array $namespaces
   * @param DOMElement $parent_element
   *
   * @return void
   */
  protected function generateNamespaceElements($namespaces, $parent_element)
  {
    foreach($namespaces as $name => $sub_namespaces)
    {
      $node = new DOMElement('namespace');
      $parent_element->appendChild($node);
      $node->setAttribute('name', $name);
      $this->generateNamespaceElements($sub_namespaces, $node);
    }
  }

  public function parseFiles($files)
  {
    $this->log('Starting to process '.count($files).' files').PHP_EOL;
    $timer = new sfTimer();

    // if the version has changed and we are not doing a full rebuild; force one
    if (($this->existing_xml)
      && ($this->existing_xml->documentElement->getAttribute('version') != DocBlox_Abstract::VERSION)
      && (!$this->isForced()))
    {
      $this->log('Version of DocBlox has changed since the last build; forcing a full re-build');
      $this->setForced(true);
    }

    // convert patterns to regex's
    foreach($this->ignore_patterns as &$pattern)
    {
      $pattern = $this->convertToPregCompliant($pattern);
    }
//    $this->log('Time1: '.round($timer->getElapsedTime(),4).'s').PHP_EOL;

    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->loadXML('<project version="'.DocBlox_Abstract::VERSION.'"></project>');

    foreach ($files as $file)
    {
      // check if the file is in an ignore pattern, if so, skip it
      foreach($this->ignore_patterns as $pattern)
      {
        if (preg_match('/^'.$pattern.'$/', $file))
        {
          $this->log('-- File "'.$file.'" matches ignore pattern, skipping');
          continue 2;
        }
      }

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
    }

    // collect all packages and store them in the XML
    // TODO: the subpackages should be collected and stored as well!
    $this->log('Collecting all packages');
    $xpath = new DOMXPath($dom);
    $packages = array();
    $qry = $xpath->query('//class/docblock/tag[@name="package"]|//file/docblock/tag[@name="package"]');
    for ($i = 0; $i < $qry->length; $i++)
    {
      $package_name = $qry->item($i)->nodeValue;
      if (isset($packages[$package_name]))
      {
        continue;
      }

      $packages[$package_name] = array();
      $qry2 = $xpath->query('//docblock/tag[@name="package" and .="'.$qry->item($i)->nodeValue.'"]/../tag[@name="subpackage"]');
      for ($i2 = 0; $i2 < $qry2->length; $i2++)
      {
        $packages[$package_name][] = $qry2->item($i2)->nodeValue;
      }
      $packages[$package_name] = array_unique($packages[$package_name]);

      // create package XMl and subpackages
      $node = new DOMElement('package');
      $dom->documentElement->appendChild($node);
      $node->setAttribute('name', $package_name);
      foreach ($packages[$package_name] as $subpackage)
      {
        $node->appendChild(new DOMElement('subpackage', $subpackage));
      }
    }

    $this->log('Collecting all namespaces');
    $xpath = new DOMXPath($dom);
    $namespaces = array();
    $qry = $xpath->query('//@namespace');
    for ($i = 0; $i < $qry->length; $i++)
    {
      if (isset($namespaces[$qry->item($i)->nodeValue]))
      {
        continue;
      }
      $namespaces[$qry->item($i)->nodeValue] = true;
    }

    $namespaces = $this->generateNamespaceTree(array_keys($namespaces));
    $this->generateNamespaceElements($namespaces, $dom->documentElement);

    $this->log('Collecting all marker types');
    foreach ($this->getMarkers() as $marker)
    {
      $node = new DOMElement('marker', strtolower($marker));
      $dom->documentElement->appendChild($node);
    }

    $dom->formatOutput = true;
    $xml = $dom->saveXML();
    $this->log('--');
    $this->log('Elapsed time to parse all files: '.round($timer->getElapsedTime(), 2).'s');
    $this->log('Peak memory usage: '.round(memory_get_peak_usage() / 1024 / 1024, 2).'M');

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