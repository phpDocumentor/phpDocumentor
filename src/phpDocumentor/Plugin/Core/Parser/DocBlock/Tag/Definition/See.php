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

/**
 * Definition for the @see tag; expands the class mentioned in the refers
 * attribute.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Plugin_Core_Parser_DocBlock_Tag_Definition_See
    extends phpDocumentor_Plugin_Core_Parser_DocBlock_Tag_Definition
{

    /**
     * Adds a new attribute `refers` to the structure element for this tag and
     * set the description to the element name.
     *
     * @return void
     */
    protected function configure()
    {
        $referral = explode('::', $this->xml['refers']);
        $referral[0] = $this->expandType($referral[0], count($referral) > 1);
        $this->xml['refers'] = implode('::', $referral);
        $this->xml['description'] = implode('::', $referral);
    }
}
