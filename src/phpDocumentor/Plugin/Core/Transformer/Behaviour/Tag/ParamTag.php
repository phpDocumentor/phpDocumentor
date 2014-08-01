<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag;

/**
 * Behaviour that adds support for the @param tags.
 */
class ParamTag
{
    /** @var string Make element name overrideable. */
    protected $element_name = 'param';

    /**
     * Find all the param tags and if using special characters transform
     * using markdown otherwise just add a <p> tag to be consistent.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $qry = '//tag[@name=\''.$this->element_name.'\']/@description[. != ""]';

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query($qry);

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            // only transform using markdown if the text contains characters
            // other than word characters, whitespaces and punctuation characters.
            // This is because Markdown is a huge performance hit on the system
            if (!preg_match('/^[\w|\s|\.|,|;|\:|\&|\#]+$/', $node->nodeValue)) {
                $md = \Parsedown::instance();
                $node->nodeValue =  $md->parse($node->nodeValue);
            } else {
                // markdown will always surround the element with a paragraph;
                // we do the same here to make it consistent
                $node->nodeValue = '&lt;p&gt;' . $node->nodeValue . '&lt;/p&gt;';
            }
        }

        return $xml;
    }
}
