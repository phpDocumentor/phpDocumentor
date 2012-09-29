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
    protected function configure()
    {
        $element = new \DOMDocument();
        $name = htmlspecialchars(
            $this->tag->getAuthorName(), ENT_NOQUOTES, 'UTF-8'
        );
        $email = $this->tag->getAuthorEmail();
        if ('' != $email) {
            $email = ' href="mailto:' . htmlspecialchars(
                $email, ENT_COMPAT, 'UTF-8'
            ) . '"';
        }
        $element->loadXML(<<<HEREDOC
<description><div xmlns="http://www.w3.org/1999/xhtml">
    <a{$email}>{$name}</a>
</div></description>
HEREDOC
        );
        $this->xml->replaceChild(
            $this->xml->ownerDocument->importNode(
                $element->documentElement, true
            ),
            $this->xml->childNodes->item(0)
        );
    }
}
