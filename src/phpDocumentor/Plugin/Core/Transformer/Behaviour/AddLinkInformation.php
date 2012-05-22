<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour;

/**
 * Behaviour that adds generated path information on the File elements.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class AddLinkInformation
    extends \phpDocumentor\Transformer\Behaviour\BehaviourAbstract
{
    /**
     * Adds extra information to the structure.
     *
     * This method enhances the Structure information with the following information:
     * - Every @see tag, or a tag with a type receives an attribute with a direct
     *   link to that tag's type entry.
     * - Every tag receives an excerpt containing the first 15 characters.
     *
     * @param \DOMDocument $xml Document for the structure file.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $this->log('Adding path information to each xml "file" tag');

        $xpath = new \DOMXPath($xml);

        $class_paths = $this->collectClassPaths($xpath);

        // add extra xml elements to tags
        $this->log('Adding link information and excerpts to all DocBlock tags');

        $qry = $xpath->query(
            '/project/file/*/docblock/tag/type[. != ""]' .
            '|/project/file/*/*/docblock/tag/type[. != ""]' .
            '|/project/file/*/extends[. != ""]' .
            '|/project/file/*/implements[. != ""]'
        );

        $declared_classes = array_flip(get_declared_classes());

        // caching array to keep track whether unknown classes are PHP Internal
        $unknown_classes  = array();

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $type = rtrim($element->nodeValue, '[]');
            $bare_type = ($type[0] == '\\') ? substr($type, 1) : $type;
            $node = $element;

            // First query the external class document links; this will override
            //     any other type; the user defined it this way with a reason
            // Then try to generate a link based on whether the class was parsed
            //     in the project.
            // Last, check whether PHP knows it and link to the PHP manual if so
            if (($link = $this->transformer
                ->findExternalClassDocumentLocation($bare_type)) !== null
            ) {
                $node->setAttribute('link', $link);
            } else if (isset($class_paths[$type])) {
                $file_name = $this->getTransformer()
                    ->generateFilename($class_paths[$type]);
                $node->setAttribute('link', $file_name . '#' . $type);
            } else if (isset($declared_classes[$bare_type])) {
                // cache reflection calls since these can be expensive
                if (!isset($unknown_classes[$bare_type])) {
                    $refl = new \ReflectionClass($bare_type);
                    $unknown_classes[$bare_type] = $refl->isInternal();
                }

                // unknown_class returns true when class is a PHP internal
                if ($unknown_classes[$bare_type]) {
                    $node->setAttribute(
                        'link',
                        'http://php.net/manual/en/class.'
                        . strtolower($bare_type) . '.php'
                    );
                }
                continue;
            }
        }

        // convert class names to links
        // this action also checks the link of an @link tag it it starts with
        // `http://`, `https://` or `www.`. if not: also convert those.
        $qry = $xpath->query(
            '//docblock/tag[@name="throw" or @name="throws" or @name="see" '
            . 'or @name="uses" or @name="used_by" or @name="inherited_from" '
            . 'or @name="covers" or @name="covered_by"]'.
            '|(//docblock/tag[@name="link" '
            . 'and (substring(@link,1,7) != \'http://\' '
            . 'or substring(@link,1,4) != \'www.\''
            . 'or substring(@link,1,7) != \'https://\')])'
        );
        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            switch($element->getAttribute('name')) {
            case 'link':
                $name = $element->getAttribute('link');
                break;
            case 'uses':
            case 'used_by':
            case 'covers':
            case 'covered_by':
            case 'see':
            case 'inherited_from':
                $name = $element->getAttribute('refers');
                if (empty($name)) {
                    $name = $element->nodeValue;
                }
                else if ($name[0] !== '\\') {
                    $name = '\\' . $name;
                }
                break;
            default:
                $name = $element->nodeValue;
                break;
            }

            $node_value = explode('::', $name);

            if (isset($class_paths[$node_value[0]])) {
                $file_name = $this->getTransformer()
                    ->generateFilename($class_paths[$node_value[0]]);
                $element->setAttribute('link', $file_name . '#' . $name);
            }
        }

        $this->processInlineLinkTags($xpath);

        return $xml;
    }

    /**
     * Collects an array of classes with their filesystem paths to use when
     * generating anchors.
     *
     * Returns an associative array where the key consists of the FQCN and the
     * value of the path that is mentioned with the 'file' element.
     *
     * @param \DOMXPath $xpath The XPath object to query against.
     *
     * @return string[]
     */
    protected function collectClassPaths(\DOMXPath $xpath)
    {
        $qry = $xpath->query('//class[full_name]|//interface[full_name]');
        $class_paths = array();

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $path = $element->parentNode->getAttribute('path');
            $class_paths[
                $element->getElementsByTagName('full_name')->item(0)->nodeValue
            ] = $path;
        }

        return $class_paths;
    }

    /**
     * Scans the document for any sign of an inline link tag and replaces it
     * with it's contents.
     *
     * This method recognizes two types of inline link tags and handles
     * them differently:
     *
     * * With description: {@link [url] [description]}, this shows the description
     *   as body of the anchor.
     * * Without description: {@link [url]}, this shows the url as body of the
     *   anchor.
     *
     * @param \DOMXPath $xpath
     *
     * @return void
     */
    protected function processInlineLinkTags(\DOMXPath $xpath)
    {
        $this->log('Adding link information to inline @link tags');

        $qry = $xpath->query('//long-description[contains(., "{@link ")]');

        // variables are used to clarify function and improve readability
        $without_description_pattern = '/\{@link\s+([^\s]+)\s*\}/';
        $with_description_pattern    = '/\{@link\s+([^\s]+)\s+([^\}]+)\}/';

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $element->nodeValue = preg_replace(
                array($without_description_pattern, $with_description_pattern),
                array('<a href="$1">$1</a>', '<a href="$1">$2</a>'),
                $element->nodeValue
            );
        }
    }
}
