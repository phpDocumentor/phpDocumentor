<?php
  // get search term
  $term = $_GET['term'];

  // find term in XML document
  $xml = new DOMDocument();
  $xml->load('search_index.xml');
  $xpath = new DOMXPath($xml);

  $qry = $xpath->query("//value[contains(., '$term')]/..");
  $results = array();

  /** @var DOMElement $element */
  foreach ($qry as $element)
  {
    /** @var DomNodeList $value  */
    $value = $element->getElementsByTagName('value');
    $id    = $element->getElementsByTagName('id');
    $type  = $element->getElementsByTagName('type');
    $results[] = '{ "value": "' . addslashes($value->item(0)->nodeValue)
      . '", "id": "' . addslashes($id->item(0)->nodeValue)
      . '", "type": "' . addslashes($type->item(0)->nodeValue) . '" }';
  }

echo '[' . implode(', ', $results) . ']';