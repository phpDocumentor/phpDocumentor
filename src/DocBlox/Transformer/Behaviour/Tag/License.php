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
 * Behaviour that enables links to URLs in the @license tag.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     David Zülke <david.zuelke@bitextender.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_Tag_License implements
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

        $licenseMap = array(
            '#^\s*(GPL|GNU General Public License)((\s?v?|version)?2)\s*$#i' => 'http://opensource.org/licenses/GPL-2.0',
            '#^\s*(GPL|GNU General Public License)((\s?v?|version)?3?)\s*$#i' => 'http://opensource.org/licenses/GPL-3.0',
            '#^\s*(LGPL|GNU (Lesser|Library) (General Public License|GPL))((\s?v?|version)?2(\.1)?)\s*$#i' => 'http://opensource.org/licenses/LGPL-2.1',
            '#^\s*(LGPL|GNU (Lesser|Library) (General Public License|GPL))((\s?v?|version)?3?)\s*$#i' => 'http://opensource.org/licenses/LGPL-3.0',
            '#^\s*((new |revised |modified |three-clause |3-clause )BSD( License)?)\s*$#i' => 'http://opensource.org/licenses/BSD-3-Clause',
            '#^\s*((simplified |two-clause |2-clause |Free)BSD)( License)?\s*$#i' => 'http://opensource.org/licenses/BSD-2-Clause',
            '#^\s*MIT( License)?\s*$#i' => 'http://opensource.org/licenses/MIT',
        );

        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query('//tag[@name="license"]/@description');

        /** @var DOMElement $node */
        foreach($nodes as $node) {

            $license = html_entity_decode($node->nodeValue, ENT_QUOTES, 'UTF-8');
            
            // FIXME: migrate to '#^' . Docblox::LINK_REGEX . '(\s+(?P<text>.+))?$#u' once that const exists
            if(preg_match('#^(?i)\b(?P<url>(?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))(\s+(?P<text>.+))?$#u', $license, $matches)) {
                if(!isset($matches['text']) || !$matches['text']) {
                    // set text to URL if not present
                    $matches['text'] = $matches['url'];
                }
                $node->parentNode->setAttribute('link', $matches['url']);
                // FIXME: #193
                $node->nodeValue = htmlspecialchars($matches['text'], ENT_QUOTES, 'UTF-8');
                // bail out early
                continue;
            }
            
            // check map if any license matches
            foreach($licenseMap as $regex => $url) {
                if(preg_match($regex, $license, $matches)) {
                    $node->parentNode->setAttribute('link', $url);
                    // we're done here
                    break;
                }
            }
            
        }

        return $xml;
    }

}