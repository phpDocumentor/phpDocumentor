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
 * Behaviour that adds support for the return tag
 */
class ReturnTag
{
    /**
     * Find all return tags that contain 'self' or '$this' and replace those
     * terms for the name of the current class' type.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $ignoreQry = '//tag[@name=\'return\' and @type=\'self\']'
            . '|//tag[@name=\'return\' and @type=\'$this\']'
            . '|//tag[@name=\'return\']/type[.=\'self\']'
            . '|//tag[@name=\'return\']/type[.=\'$this\']';

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            // if a node with name 'type' is selected we need to reach one
            // level further.
            $docblock = ($node->nodeName == 'type')
                ? $node->parentNode->parentNode
                : $node->parentNode;

            /** @var \DOMElement $method  */
            $method = $docblock->parentNode;

            // if the method is not a method but a global function: error!
            if ($method->nodeName != 'method') {
                continue;
            }

            $type = $method->parentNode->getElementsByTagName('full_name')
                ->item(0)->nodeValue;

            // nodes with name type need to set their content; otherwise we set
            // an attribute of the class itself
            if ($node->nodeName == 'type') {
                $node->nodeValue = $type;

                // add a new tag @fluent to indicate that this is a fluent interface
                // we only add it here since there should always be a node `type`
                $fluent_tag = new \DOMElement('tag');
                $docblock->appendChild($fluent_tag);
                $fluent_tag->setAttribute('name', 'fluent');
                $fluent_tag->setAttribute(
                    'description',
                    'This method is part of a fluent interface and will return '
                    . 'the same instance'
                );
            } else {
                $node->setAttribute('type', $type);
            }

            // check if an excerpt is set and override that as well
            if ($node->hasAttribute('excerpt')
                && (($node->getAttribute('excerpt') == 'self')
                || ($node->getAttribute('excerpt') == '$this'))
            ) {
                $node->setAttribute('excerpt', $type);
            }
        }

        return $xml;
    }
}
