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
 * Behaviour that adds support for @ignore tag.
 */
class IgnoreTag
{
    protected $tag = 'ignore';

    /**
     * Removes DocBlocks marked with 'ignore' tag from the structure.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $ignoreQry = '//tag[@name=\''. $this->tag . '\']';

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $remove = $node->parentNode->parentNode;

            // sometimes the parent node of the entity-to-be-removed is already
            // gone; for instance when a File docblock contains an @internal and
            // the underlying class also contains an @internal.
            // Because the File Docblock is found sooner, it is removed first.
            // Without the following check the application would fatal since
            // it cannot find, and then remove, this node from the parent.
            if (!isset($remove->parentNode)) {
                continue;
            }

            $remove->parentNode->removeChild($remove);
        }

        return $xml;
    }
}
