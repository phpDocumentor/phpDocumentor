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
class AddLinkInformation extends \phpDocumentor\Transformer\Behaviour\BehaviourAbstract
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
            } elseif (isset($class_paths[$type])) {
                $file_name = $this->getTransformer()
                    ->generateFilename($class_paths[$type]);
                $node->setAttribute('link', $file_name . '#' . $type);
            } elseif (isset($declared_classes[$bare_type])) {
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
                    elseif ($name[0] !== '\\') {
                        $name = '\\' . $name;
                    }
                    break;
                default:
                    $name = $element->nodeValue;
                    break;
            }

            $node_value = explode('::', $name);

            if (isset($class_paths[$node_value[0]])) {
                $file_name = $this->getTransformer()->generateFilename($class_paths[$node_value[0]]);
                $element->setAttribute('link', $file_name . '#' . $name);
            }
        }

        $this->processInlineLinkTags($xpath);
		$this->processInlineSeeTags($xpath);

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
            $class_paths[$element->getElementsByTagName('full_name')->item(0)->nodeValue] = $path;
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

    /**
     * Scans the document for any sign of an inline see tag and replaces it
     * with a link to the respective element documentation.
     *
     * This method recognizes two types of inline link tags and handles
     * them differently:
     *
     * * With description: {@see [element] [description]}, this shows the description
     *   as body of the anchor.
     * * Without description: {@link [element]}, this shows the element as body of the
     *   anchor.
     *
     * @param \DOMXPath $xpath
     *
     * @return void
     */
    protected function processInlineSeeTags(\DOMXPath $xpath)
    {
        $this->log('Adding link information to inline @see tags');

        $qry = $xpath->query('//long-description[contains(., "{@see ")]');

        // variables are used to clarify function and improve readability
        $without_description_pattern = '/\{@see\s+([^\s]+)\s*\}/';
        $with_description_pattern    = '/\{@see\s+([^\s]+)\s+([^\}]+)\}/';

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
			preg_match_all(
				$without_description_pattern,
				$element->nodeValue,
				$matches,
				PREG_SET_ORDER
			);
			foreach ($matches as $match) {
				$element->nodeValue = str_replace(
					$match[0],
					$this->getSeeLink($element, $match[1], $match[1]),
					$element->nodeValue
				);
			}
			
			preg_match_all(
				$with_description_pattern,
				$element->nodeValue,
				$matches,
				PREG_SET_ORDER
			);
			foreach ($matches as $match) {
				$element->nodeValue = str_replace(
					$match[0],
					$this->getSeeLink($element, $match[1], $match[2]),
					$element->nodeValue
				);
			}
		}
    }

	/**
	 * Parses the inline see tag and returns the corresponding HTML anchor tag.
	 * 
	 * This method creates the HTML anchor tag for the given class, property, or
	 * method. The description may be omitted.
	 * 
	 * @param \DOMElement $element
	 * @param stiring $see
	 * @param string $description
	 * @return string
	 */
	protected function getSeeLink($element, $see, $description = '') {
		$type = 'class';
		if ($see[0] === '$') {
			$type = 'property';
		} else if (substr($see, -2) === '()') {
			$type = 'method';
		}
		
		$file_name = '';
		switch ($type) {
			case 'class':
				$file_name = $this->getTransformer()
					->generateFilename($see);
				break;

			case 'property':
			case 'method':
				$see_parts = explode('::', $see);
				if (count($see_parts) === 2) {
					$file_name = $this->getTransformer()
						->generateFilename($see_parts[0])
						. '#' . $type . '_'
						. str_replace('()', '', $see_parts[1]);
				} else {
					$file_name = $this->getTransformer()
						->generateFilename($this->getParentClass($element))
						. '#' . $type . '_'
						. str_replace('()', '', $see);
				}
				break;
		}
		return '<a href="' . $file_name . '">' . (empty($description) ? $see : $description) . '</a>';
	}

	/**
	 * Finds the parent class element.
	 * 
	 * This method searches for a parent class element and the class'
	 * name which is returned when found. If a class element or a
	 * name element could not be found, an empty string is returned.
	 * 
	 * @param \DOMElement $element
	 * @return string
	 */
	protected function getParentClass($element) {
		if ($element->tagName == 'class') {
			$current_element = null;
			for ($i = 0; $i <= $element->childNodes->length; $i++) {
				if ($current_element === null) {
					$current_element = $element->firstChild;
				} else {
					$current_element = $current_element->nextSibling;
				}
				if (
					property_exists($current_element, 'tagName')
					&& $current_element->tagName === 'name'
				) {
					return $current_element->textContent;
				}
			}
			return '';
		} else {
			if ($element->parentNode) {
				return $this->getParentClass($element->parentNode);
			} else {
				return '';
			}
		}
	}
}
