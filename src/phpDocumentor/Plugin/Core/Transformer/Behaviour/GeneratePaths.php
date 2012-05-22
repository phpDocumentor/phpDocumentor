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
class GeneratePaths extends \phpDocumentor\Transformer\Behaviour\BehaviourAbstract
{

    /**
     * Adds extra information to the structure.
     *
     * This method enhances the Structure information with the following
     * information:
     *
     * - Every file receives a 'generated-path' attribute which contains the
     *   path on the filesystem where the docs for that file van be found.
     *
     * @param \DOMDocument $xml Structure source to modify.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $this->log('Adding path information to each xml "file" tag');

        $xpath = new \DOMXPath($xml);
        $qry = $xpath->query("/project/file[@path]");

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $files[] = $element->getAttribute('path');
            $element->setAttribute(
                'generated-path',
                $this->getTransformer()->generateFilename(
                    $element->getAttribute('path')
                )
            );
        }

        return $xml;
    }

}