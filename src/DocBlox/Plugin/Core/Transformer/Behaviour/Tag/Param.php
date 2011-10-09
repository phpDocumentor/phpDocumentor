<?php
/**
 * DocBlox
 *
 * PHP 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that adds support for the @param tag
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Param extends
    DocBlox_Transformer_Behaviour_Abstract
{
    /**
     * Find all the @param tags and if using special characters transform
     * using markdown otherwise just add a <p> tag to be consistent
     *
     * @param DOMDocument $xml Xml document we are wanting to query and manipulate
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        $qry = '//tag[@name=\'param\']/@description[. != ""]';

        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query($qry);

        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            // only transform using markdown if the text contains characters
            // other than word characters, whitespaces and punctuation characters.
            // This is because Markdown is a huge performance hit on the system
            if (!preg_match('/^[\w|\s|\.|,|;|\:|\&|\#]+$/', $node->nodeValue)) {
                $node->nodeValue = Markdown($node->nodeValue);
            } else {
                // markdown will always surround the element with a paragraph;
                // we do the same here to make it consistent
                $node->nodeValue = '&lt;p&gt;' . $node->nodeValue . '&lt;/p&gt;';
            }
        }

        return $xml;
    }
}