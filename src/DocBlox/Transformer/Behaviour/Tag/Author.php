<?php
/**
 * DocBlox
 *
 * PHP 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license	   http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that links to email addresses in the @author tag.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     David Zülke <david.zuelke@bitextender.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_Tag_Author implements
    DocBlox_Transformer_Behaviour_Interface
{
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
     * Find all return tags that contain 'self' or '$this' and replace those
     * terms for the name of the current class' type.
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        if ($this->logger){
            $this->logger->log(
                'Linking to email addresses in @author tags'
            );
        }

        // matches:
        // - foo@bar.com
        // - <foo@bar.com>
        // - Some Name <foo@bar.com>
        // ignores leading and trailing whitespace
        // requires angled brackets when a name is given (that's what the two (?(1)) conditions do)
        // requires closing angled bracket if email address is given with an opening angled bracket but no name (that's what the (?(3)) condition is for)
        $regex = '#^\s*(?P<name>[^<]+?)?\s*((?(1)<|<?)(?:mailto:)?(?P<email>\b[a-z0-9._%-]+@[a-z0-9.-]+\.[a-z]{2,4}\b)(?(1)>|(?(3)>|>?)))\s*$#u';

        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query('//tag[@name="author"]/@description');

        /** @var DOMElement $node */
        foreach($nodes as $node) {

            // FIXME: #193
            if(preg_match($regex, html_entity_decode($node->nodeValue, ENT_QUOTES, 'UTF-8'), $matches)) {
                if($matches['name']) {
                    $value = $matches['name'];
                } else {
                    $value = $matches['email']; // in case there were <> but no name... this cleans up the output a bit
                }
                
                // FIXME: #193
                $node->nodeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                
                $node->parentNode->setAttribute('link', 'mailto:' . $matches['email']);
            }

        }

        return $xml;
    }

}