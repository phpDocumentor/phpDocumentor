<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag;

/**
 * Behaviour that adds support for @internal tag.
 */
class InternalTag extends IgnoreTag
{
    protected $tag = 'internal';

    /**
     * Removes DocBlocks marked with 'internal' tag from the structure.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        if (!$this->getTransformer()->getParseprivate()) {
            $xml = parent::process($xml);
        }

        $ignoreQry = '//long-description[contains(., "{@internal")]';

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        // either replace it with nothing or with the 'stored' value
        $replacement = $this->getTransformer()->getParseprivate() ? '$1' : '';

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $node->nodeValue = preg_replace('/\{@internal\s(.+?)\}\}/', $replacement, $node->nodeValue);
        }

        return $xml;
    }
}
