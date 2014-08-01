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

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag;

/**
 * Behaviour that enables links to URLs in the @license tag.
 */
class LicenseTag
{
    /**
     * Find all return tags that contain 'self' or '$this' and replace those
     * terms for the name of the current class' type.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $licenseMap = array(
            '#^\s*(GPL|GNU General Public License)((\s?v?|version)?2)\s*$#i'
                => 'http://opensource.org/licenses/GPL-2.0',
            '#^\s*(GPL|GNU General Public License)((\s?v?|version)?3?)\s*$#i'
                => 'http://opensource.org/licenses/GPL-3.0',
            '#^\s*(LGPL|GNU (Lesser|Library) (General Public License|GPL))'
                .'((\s?v?|version)?2(\.1)?)\s*$#i'
                => 'http://opensource.org/licenses/LGPL-2.1',
            '#^\s*(LGPL|GNU (Lesser|Library) (General Public License|GPL))'
                .'((\s?v?|version)?3?)\s*$#i'
                => 'http://opensource.org/licenses/LGPL-3.0',
            '#^\s*((new |revised |modified |three-clause |3-clause )BSD'
                .'( License)?)\s*$#i'
                => 'http://opensource.org/licenses/BSD-3-Clause',
            '#^\s*((simplified |two-clause |2-clause |Free)BSD)( License)?\s*$#i'
                => 'http://opensource.org/licenses/BSD-2-Clause',
            '#^\s*MIT( License)?\s*$#i' => 'http://opensource.org/licenses/MIT',
        );

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query('//tag[@name="license"]/@description');

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {

            $license = $node->nodeValue;

            // FIXME: migrate to '#^' . PHPDOC::LINK_REGEX . '(\s+(?P<text>.+))
            // ?$#u' once that const exists
            if (preg_match(
                '#^(?i)\b(?P<url>(?:https?://|www\d{0,3}\.|[a-z0-9.\-]+\.'
                .'[a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+'
                .'(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|'
                .'[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))(\s+(?P<text>.+))?$#u',
                $license,
                $matches
            )) {
                if (!isset($matches['text']) || !$matches['text']) {
                    // set text to URL if not present
                    $matches['text'] = $matches['url'];
                }
                $node->parentNode->setAttribute('link', $matches['url']);
                $node->nodeValue = $matches['text'];

                // bail out early
                continue;
            }

            // check map if any license matches
            foreach ($licenseMap as $regex => $url) {
                if (preg_match($regex, $license, $matches)) {
                    $node->parentNode->setAttribute('link', $url);

                    // we're done here
                    break;
                }
            }
        }

        return $xml;
    }
}
