<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviours
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that adds support for @internal tag.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviours
 * @author     Stepan Anchugov <kix@kixlive.ru>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Internal extends
    DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Ignore
{
    protected $tag = 'internal';

    /**
     * Removes DocBlocks marked with 'internal' tag from the structure.
     *
     * @param DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        if (!$this->getTransformer()->getParseprivate()) {
            $xml = parent::process($xml);
        }

        $this->log('Removing @internal inline tags');

        $ignoreQry = '//long-description[contains(., "{@internal")]';

        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query($ignoreQry);

        // either replace it with nothing or with the 'stored' value
        $replacement = $this->getTransformer()->getParseprivate() ? '$1' : '';

        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $node->nodeValue = preg_replace(
                '/\{@internal\s(.+?)\}\}/', $replacement, $node->nodeValue
            );
        }

        return $xml;
    }


}