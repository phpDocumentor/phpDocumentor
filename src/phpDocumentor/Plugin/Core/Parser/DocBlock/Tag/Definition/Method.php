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
 * Definition for the @param tag; adds a attribute called `variable`.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Method extends Definition
{
    /**
     * Adds an attribute called `variable` containing the name of the argument.
     *
     * @return void
     */
    protected function configure()
    {
        /** @var \phpDocumentor\Reflection\DocBlock\Tag\MethodTag $tag */
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
