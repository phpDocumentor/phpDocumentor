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
 * Behaviour that adds support for @internal inline tag.
 */
class InternalTag
{
    /** @var boolean $internalAllowed true if the `@internal` tags should be rendered */
    protected $internalAllowed;

    /**
     * Initializes this tag and describes whether it should be rendered in the output.
     *
     * @param boolean $internalAllowed
     */
    public function __construct($internalAllowed)
    {
        $this->internalAllowed = $internalAllowed;
    }

    /**
     * Converts the 'internal' tags in Long Descriptions.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @todo This behaviours actions should be moved to the parser / Reflector builder so that it can be cached
     *     and is available to all writers.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $ignoreQry = '//long-description[contains(., "{@internal")]';

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        // either replace it with nothing or with the 'stored' value
        $replacement = $this->internalAllowed ? '$1' : '';

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $node->nodeValue = preg_replace('/\{@internal\s(.+?)\}\}/', $replacement, $node->nodeValue);
        }

        return $xml;
    }
}
