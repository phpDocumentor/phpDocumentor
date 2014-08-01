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
 * Behaviour that adds support for the @method tag
 */
class MethodTag
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
        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query(
            '/project/file/class/docblock/tag[@name="method"]'
        );

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $class = $node->parentNode->parentNode;

            $method = new \DOMElement('method');
            $class->appendChild($method);

            // set basic information
            $method->setAttribute('final', 'false');
            $method->setAttribute('static', 'false');
            $method->setAttribute('visibility', 'public');
            $method->setAttribute('line', $node->getAttribute('line'));
            $method->appendChild(
                new \DOMElement('name', $node->getAttribute('method_name'))
            );

            // fill docblock
            $docblock = new \DOMElement('docblock');
            $method->appendChild($docblock);
            $docblock->appendChild(
                new \DOMElement('description', $node->getAttribute('description'))
            );
            $docblock->appendChild(new \DOMElement('long-description'));

            // for each argument; create an @param tag and an argument element
            /** @var \DOMElement $argument */
            foreach ($node->getElementsByTagName('argument') as $argument) {
                $param_tag = new \DOMElement('tag');
                $docblock->appendChild($param_tag);
                $param_tag->setAttribute('name', 'param');
                $param_tag->setAttribute(
                    'type',
                    $argument->getElementsByTagName('type')->item(0)->nodeValue
                );
                $param_tag->setAttribute(
                    'variable',
                    $argument->getElementsByTagName('name')->item(0)->nodeValue
                );
                $param_tag->setAttribute('line', $node->getAttribute('line'));

                $types = explode(
                    '|',
                    $argument->getElementsByTagName('type')->item(0)->nodeValue
                );

                foreach ($types as $type) {
                    $type_element = new \DOMElement('type', $type);
                    $param_tag->appendChild($type_element);
                }

                $argument_element = $argument->cloneNode(true);
                $method->appendChild($argument_element);
            }

            // add a tag 'magic'
            $magic_tag = new \DOMElement('tag');
            $docblock->appendChild($magic_tag);
            $magic_tag->setAttribute('name', 'magic');
            $magic_tag->setAttribute('line', $node->getAttribute('line'));

            // add a @return tag
            $return_tag = new \DOMElement('tag');
            $docblock->appendChild($return_tag);
            $return_tag->setAttribute('name', 'return');
            $return_tag->setAttribute('line', $node->getAttribute('line'));
            $return_tag->setAttribute('type', $node->getAttribute('type'));

            // add type sub elements to the param
            foreach (explode('|', $node->getAttribute('type')) as $type) {
                $type_element = new \DOMElement('type', $type);
                $return_tag->appendChild($type_element);
            }

            // remove the tag, it is unneeded
            $node->parentNode->removeChild($node);
            $docblock->appendChild($node);
        }

        return $xml;
    }
}
