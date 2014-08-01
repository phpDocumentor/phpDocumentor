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

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata;

/**
 * The glossary is a collection containing a collection of terms and uses that were discovered during the discovery
 * phase.
 *
 * The key of this collection is the term that was discovered and the value is an array of locations where the term
 * was used.
 *
 * The array of locations consists of a filename as key and an array with the linenumbers where the term occurred.
 *
 * Example:
 *
 * array(1) {
 *   ["term"]=>
 *   array(1) {
 *     ["filename"]=>
 *     array(3) {
 *       [0]=> int(10)
 *       [1]=> int(14)
 *       [2]=> int(20)
 *     }
 *   }
 * }
 */
class Glossary extends \ArrayObject
{
    /**
     * Adds a glossary term to the collection.
     *
     * @param string $term
     * @param string $filename
     * @param int    $line_number
     *
     * @return void
     */
    public function addTerm($term, $filename, $line_number)
    {
        if (!isset($this[$term])) {
            $this[$term] = array();
        }
        if (!isset($this[$term][$filename])) {
            $this[$term][$filename] = array();
        }

        $this[$term][$filename][] = $line_number;
    }
}
