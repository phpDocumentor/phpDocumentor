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
 * Definition for the @link tag; adds a attribute called `link`.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Link extends Definition
{

    /**
     * Adds a new attribute `link` to the structure element for this tag.
     *
     * @throws InvalidArgumentException if the associated tag is not of type Link.
     *
     * @return void
     */
    protected function configure()
    {
        if (!$this->tag instanceof \phpDocumentor\Reflection\DocBlock\Tag\LinkTag) {
            throw new \InvalidArgumentException(
                'Expected the tag to be for an @link'
            );
        }

        $this->xml['link'] = $this->tag->getLink();
    }
}
