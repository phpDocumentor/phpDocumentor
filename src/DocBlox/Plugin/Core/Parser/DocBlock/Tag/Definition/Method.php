<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Definition for the @param tag; adds a attribute called `variable`.
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Parser_DocBlock_Tag_Definition_Method
    extends DocBlox_Plugin_Core_Parser_DocBlock_Tag_Definition
{
    /**
     * Adds an attribute called `variable` containing the name of the argument.
     *
     * @return void
     */
    protected function configure()
    {
        /** @var DocBlox_Reflection_DocBlock_Tag_Method $tag */
        $tag = $this->tag;

        $this->xml['method_name'] = $tag->getMethodName();

        foreach ($tag->getArguments() as $argument) {
            $argument_obj = $this->xml->addChild('argument');
            $argument_obj->addChild(
                'name', $argument[count($argument) > 1 ? 1 : 0]
            );
            $argument_obj->addChild('default');
            $argument_obj->addChild(
                'type', count($argument) > 1 ? $argument[0] : null
            );
        }
    }
}
