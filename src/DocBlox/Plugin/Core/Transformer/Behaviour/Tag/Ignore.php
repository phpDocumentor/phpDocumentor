<?php
/**
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Stepan Anchugov <kix@kixlive.ru>
 * @license	   http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that adds support for @ignore tag
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behavior
 * @author     Stepan Anchugov <kix@kixlive.ru>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Ignore extends
    DocBlox_Transformer_Behaviour_Abstract
{
    protected $tag = 'ignore';

    /**
     * Removes DocBlocks marked with 'ignore' tag from the structure
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        $this->log('Removing DocBlocks containing the @'.$this->tag.' tag');

        $ignoreQry = '//tag[@name=\''. $this->tag . '\']';

        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        /** @var DOMElement $node */
        foreach($nodes as $node) {
            $remove = $node->parentNode->parentNode;

            // sometimes the parent node of the entity-to-be-removed is already
            // gone; for instance when a File docblock contains an @internal and
            // the underlying class also contains an @internal.
            // Because the File Docblock is found sooner, it is removed first.
            // Without the following check the application would fatal since
            // it cannot find, and then remove, this node from the parent.
            if (!isset($remove->parentNode)) {
                continue;
            }

            $remove->parentNode->removeChild($remove);
        }

        return $xml;
    }

}