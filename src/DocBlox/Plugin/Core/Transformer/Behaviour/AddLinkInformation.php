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
class DocBlox_Plugin_Core_Transformer_Behaviour_AddLinkInformation extends
    DocBlox_Transformer_Behaviour_Abstract
{
    /**
     * Adds extra information to the structure.
     *
     * This method enhances the Structure information with the following information:
     * - Every @see tag, or a tag with a type receives an attribute with a direct
     *   link to that tag's type entry.
     * - Every tag receives an excerpt containing the first 15 characters.
     *
     * @param DOMDocument $xml Document for the structure file.
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        $this->log('Adding path information to each xml "file" tag');

        $xpath = new DOMXPath($xml);
        // add to classes
        $qry = $xpath->query('//class[full_name]|//interface[full_name]');
        $class_paths = array();

        /** @var DOMElement $element */
        foreach ($qry as $element) {
            $path = $element->parentNode->getAttribute('path');
            $class_paths[
                $element->getElementsByTagName('full_name')->item(0)->nodeValue
            ] = $path;
        }

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

        /** @var DOMElement $element */
        foreach ($qry as $element) {
            $type = rtrim($element->nodeValue, '[]');
            $bare_type = ($type[0] == '\\') ? substr($type, 1) : $type;
            $node = $element;

            // if the class is already loaded and is an internal class; refer
            // to the PHP man pages
            if (isset($class_paths[$type])) {
                $file_name = $this->generateFilename($class_paths[$type]);
                $node->setAttribute('link', $file_name . '#' . $type);
            } else if (isset($declared_classes[$bare_type])) {
                // cache reflection calls since these can be expensive
                if (!isset($unknown_classes[$bare_type])) {
                    $refl = new ReflectionClass($bare_type);
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
            } else {
                // an undeclared class but not internal to PHP
                $link = $this->transformer->findExternalClassDocumentLocation(
                    $bare_type
                );

                if ($link !== null) {
                    $node->setAttribute('link', $link);
                }
            }

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
        foreach ($qry as $element) {
            switch($element->getAttribute('name')) {
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
     * @param string $file Base name of the source file.
     *
     * @return string
     */
    public function generateFilename($file)
    {
        $info = pathinfo(
            str_replace(
                DIRECTORY_SEPARATOR,
                '_',
                trim($file, DIRECTORY_SEPARATOR . '.')
            )
        );

        return 'db_' . $info['filename'] . '.html';
    }

}