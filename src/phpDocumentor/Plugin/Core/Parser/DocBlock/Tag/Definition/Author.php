<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Parser\DocBlock\Tag\Definition;

/**
 * Definition for the @uses tag; expands the class mentioned in the refers
 * attribute.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Author extends Definition
{
    public function setDescription($description)
    {
        $this->xml->appendChild(new \DOMElement('description', $description));
    }

    protected function configure()
    {
        $this->xml->appendChild(
            new \DOMElement('name', $this->tag->getAuthorName())
        );
        $this->xml->appendChild(
            new \DOMElement('email', $this->tag->getAuthorEmail())
        );
    }
}
