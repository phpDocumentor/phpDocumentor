<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
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
 * @package    Transformer
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
    public function setLogger(DocBlox_Core_Log $log = null)
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
        if ($this->logger) {
            $this->logger->log('Adding link information and excerpts to all DocBlock tags');
        }
        $qry = $xpath->query('//docblock/tag/@type|//docblock/tag/type|//extends|//implements');

        $declared_classes = get_declared_classes();

        /** @var DOMElement $element */
        foreach ($qry as $element)
        {
            $type = rtrim($element->nodeValue, '[]');
            $node = ($element->nodeType == XML_ATTRIBUTE_NODE)
                    ? $element->parentNode
                    : $element;

            // if the class is already loaded and is an internal class; refer
            // to the PHP man pages
            if (in_array(ltrim($type, '\\'), $declared_classes)) {
                $refl = new ReflectionClass(ltrim($type, '\\'));
                if ($refl->isInternal()) {
                    $node->setAttribute(
                        'link',
                        'http://php.net/manual/en/class.'
                        . strtolower(ltrim($type, '\\')) . '.php'
                    );
                }
            }

            if (isset($class_paths[$type])) {
                $file_name = $this->generateFilename($class_paths[$type]);
                $node->setAttribute('link', $file_name . '#' . $type);
            }

            // add a 15 character excerpt of the node contents, meant for the sidebar
            $node->setAttribute(
                'excerpt',
                utf8_encode(substr($type, 0, 15) . (strlen($type) > 15 ? '...' : ''))
            );
        }

        // convert class names to links
        // this action also checks the link of an @link tag it it starts with
        // `http://`, `https://` or `www.`. if not: also convert those.
        $qry = $xpath->query(
            '//docblock/tag[@name="throw" or @name="throws" or @name="see" '
            . 'or @name="uses" or @name="used_by" or @name="inherited_from"]'.
            '|(//docblock/tag[@name="link" '
            . 'and (substring(@link,1,7) != \'http://\' '
            . 'or substring(@link,1,4) != \'www.\''
            . 'or substring(@link,1,7) != \'https://\')])'
        );
        /** @var DOMElement $element */
        foreach ($qry as $element)
        {
            switch($element->getAttribute('name'))
            {
                case 'link':
                    $name = $element->getAttribute('link');
                    break;
                case 'uses':
                case 'used_by':
                case 'see':
                case 'inherited_from':
                    $name = $element->getAttribute('refers');
                    if ($name[0] !== '\\') {
                        $name = '\\' . $name;
                    }
                    break;
                default:
                    $name = $element->nodeValue;
                    break;
            }

            $node_value = explode('::', $name);

            if (isset($class_paths[$node_value[0]])) {
                $file_name = $this->generateFilename($class_paths[$node_value[0]]);
                $element->setAttribute('link', $file_name . '#' . $name);
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