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
class DocBlox_Transformer_Behaviour_Tag_Ignore implements
    DocBlox_Transformer_Behaviour_Interface
{
    protected $tag = 'ignore';

    /** @var DocBlox_Core_Log */
    protected $logger = null;

    /**
     * Sets the logger for this behaviour.
     *
     * @param DocBlox_Core_Log $log
     *
     * @return void
     */
    public function setLogger(DocBlox_Core_Log $log = null)
    {
        $this->logger = $log;
    }

    /**
     * Removes DocBlocks marked with 'ignore' tag from the structure
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        if ($this->logger){
            $this->logger->log(
                'Removing DocBlocks containing the @'.$this->tag.' tag'
            );
        }

        $ignoreQry = '//tag[@name=\''. $this->tag . '\']';

        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        /** @var DOMElement $node */
        foreach($nodes as $node) {
            $remove = $node->parentNode->parentNode;
            $node->parentNode->parentNode->parentNode->removeChild($remove);
        }

        return $xml;
    }

}