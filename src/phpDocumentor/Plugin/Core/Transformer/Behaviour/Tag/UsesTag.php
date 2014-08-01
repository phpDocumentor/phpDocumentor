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
 * Behaviour that adds support for the uses tag
 */
class UsesTag
{
    /**
     * Find all return tags that contain 'self' or '$this' and replace those
     * terms for the name of the current class' type.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @todo split method into submethods
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query('//tag[@name=\'uses\']');

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $refers = $node->getAttribute('refers');
            $refers_array = explode('::', $refers);

            // determine the type so we know where to put the @usedby tag on
            $type = 'class';
            if (isset($refers_array[1])) {
                // starts with $ = property, ends with () = method,
                // otherwise constant
                $type = $refers_array[1][0] == '$' ? 'property' : 'constant';
                $type = substr($refers_array[1], -2) == '()' ? 'method' : $type;
            }

            switch ($type) {
                case 'class':
                    // escape single quotes in the class name
                    $xpath_refers = 'concat(\''.str_replace(
                        array("'", '"'),
                        array('\', "\'", \'', '\', \'"\' , \''),
                        $refers
                    ) . "', '')";

                    $qry = '/project/file/class[full_name=' . $xpath_refers . ']';
                    break;
                default:
                    $class_name = $refers_array[0];

                    // escape single quotes in the class name
                    $xpath_class_name = 'concat(\''.str_replace(
                        array("'", '"'),
                        array('\', "\'", \'', '\', \'"\' , \''),
                        $class_name
                    ) . "', '')";

                    // escape single quotes in the method name
                    $xpath_method_name = 'concat(\''.str_replace(
                        array("'", '"'),
                        array('\', "\'", \'', '\', \'"\' , \''),
                        rtrim($refers_array[1], '()')
                    ) . "', '')";

                    $qry = '/project/file/class[full_name=' . $xpath_class_name
                        . ']/'.$type.'[name=' . $xpath_method_name .']';
                    break;
            }

            /** @noinspection PhpUsageOfSilenceOperatorInspection as there is no pre-validation possible */
            $referral_nodes = @$xpath->query($qry);

            // if the query is wrong; output a Critical error and continue to
            // the next @uses
            if ($referral_nodes === false) {
                continue;
            }

            // check if the result is unique; if not we error and continue
            // to the next @uses
            if ($referral_nodes->length > 1) {
                continue;
            }

            // if there is one matching element; link them together
            if ($referral_nodes->length > 0) {
                /** @var \DOMElement $referral */
                $referral = $referral_nodes->item(0);
                $docblock = $referral->getElementsByTagName('docblock');
                if ($docblock->length < 1) {
                    $docblock = new \DOMElement('docblock');
                    $referral->appendChild($docblock);
                } else {
                    $docblock = $docblock->item(0);
                }

                $used_by = new \DOMElement('tag');
                $docblock->appendChild($used_by);
                $used_by->setAttribute('name', 'used_by');
                $used_by->setAttribute('line', '');

                // gather the name of the referring element and set that as refers
                // attribute
                if ($node->parentNode->parentNode->nodeName == 'class') {
                    // if the element where the @uses is in is a class; nothing
                    // more than the class name need to returned
                    $referral_name = $node->parentNode->parentNode
                        ->getElementsByTagName('full_name')->item(0)->nodeValue;
                } else {

                    $referral_class_name = null;
                    if ($node->parentNode->parentNode->nodeName == 'method') {
                        // gather the name of the class where the @uses is in
                        $referral_class_name = $node->parentNode->parentNode
                            ->parentNode->getElementsByTagName('full_name')->item(0)
                            ->nodeValue;
                    }

                    // gather the name of the subelement of the class where
                    // the @uses is in
                    $referral_name = $node->parentNode->parentNode
                        ->getElementsByTagName('name')->item(0)->nodeValue;

                    // if it is a method; suffix with ()
                    if ($node->parentNode->parentNode->nodeName == 'method'
                        || $node->parentNode->parentNode->nodeName == 'function'
                    ) {
                        $referral_name .= '()';
                    }

                    // only prefix class name if this is a class member
                    if ($referral_class_name) {
                        $referral_name = $referral_class_name . '::'
                            . $referral_name;
                    }
                }

                $used_by->setAttribute('description', $referral_name);
                $used_by->setAttribute('refers', $referral_name);
            }
        }

        return $xml;
    }
}
