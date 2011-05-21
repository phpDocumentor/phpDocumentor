<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformation
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that adds generated path information on the File elements.
 *
 * @category   DocBlox
 * @package    Transformation
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_AddLinkInformation implements
    DocBlox_Transformer_Behaviour_Interface
{
    /** @var DocBlox_Core_Log */
    protected $logger = null;

    /**
     * Sets the logger for this behaviour.
     *
     * @param DocBlox_Core_Log $log
     *
     * @return void
     */
    public function setLogger(DocBlox_Core_Log $log)
    {
        $this->logger = $log;
    }

    /**
     * Adds extra information to the structure.
     *
     * This method enhances the Structure information with the following information:
     * - Every @see tag, or a tag with a type receives an attribute with a direct link to that tag's type entry.
     * - Every tag receives an excerpt containing the first 15 characters.
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        if ($this->logger) {
            $this->logger->log('Adding path information to each xml "file" tag');
        }

        $xpath = new DOMXPath($xml);
        // add to classes
        $qry = $xpath->query('//class[full_name]/..');
        $class_paths = array();

        /** @var DOMElement $element */
        foreach ($qry as $element)
        {
            $path = $element->getAttribute('path');
            foreach ($element->getElementsByTagName('class') as $class)
            {
                $class_paths[$class->getElementsByTagName('full_name')->item(0)->nodeValue] = $path;
            }
        }

        // add to interfaces
        $qry = $xpath->query('//interface[full_name]/..');
        /** @var DOMElement $element */
        foreach ($qry as $element)
        {
            $path = $element->getAttribute('path');

            /** @var DOMElement $class */
            foreach ($element->getElementsByTagName('interface') as $class)
            {
                $class_paths[$class->getElementsByTagName('full_name')->item(0)->nodeValue] = $path;
            }
        }

        // add extra xml elements to tags
        $this->logger->log('Adding link information and excerpts to all DocBlock tags');
        $qry = $xpath->query('//docblock/tag/@type|//docblock/tag/type|//extends|//implements');

        /** @var DOMElement $element */
        foreach ($qry as $element)
        {
            $type = rtrim($element->nodeValue, '[]');
            $node = ($element->nodeType == XML_ATTRIBUTE_NODE)
                    ? $element->parentNode
                    : $element;

            if (isset($class_paths[$type])) {
                $file_name = $this->generateFilename($class_paths[$type]);
                $node->setAttribute('link', $file_name . '#' . $type);
            }

            // add a 15 character excerpt of the node contents, meant for the sidebar
            $node->setAttribute('excerpt', utf8_encode(substr($type, 0, 15) . (strlen($type) > 15 ? '...' : '')));
        }


        $qry = $xpath->query('//docblock/tag[@name="see" or @name="throw" or @name="throws"]');
        /** @var DOMElement $element */
        foreach ($qry as $element)
        {
            $node_value = explode('::', $element->nodeValue);
            if (isset($class_paths[$node_value[0]])) {
                $file_name = $this->generateFilename($class_paths[$node_value[0]]);
                $element->setAttribute('link', $file_name . '#' . $element->nodeValue);
            }
        }

        return $xml;
    }

    /**
     * Converts a source file name to the name used for generating the end result.
     *
     * @param string $file
     *
     * @return string
     */
    public function generateFilename($file)
    {
        $info = pathinfo(str_replace(DIRECTORY_SEPARATOR, '_', trim($file, DIRECTORY_SEPARATOR . '.')));
        return '_' . $info['filename'] . '.html';
    }

}