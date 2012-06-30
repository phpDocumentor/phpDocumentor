<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Inherit\Node;

/**
 * Responsible for adding inheritance behaviour to an individual method.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class MethodNode extends PropertyNode
{
    /** @var string[] Defined which tags to inherit */
    protected $inherited_tags = array('param', 'return', 'throw', 'throws');

    /**
     * Returns the full string identifier of this method.
     *
     * Example:
     *
     *     MyClass::method()
     *
     * @param string|null $parent_class_name The class name to use; if null
     *     uses the current class name.
     *
     * @return string
     */
    public function getReferrerString($parent_class_name = null)
    {
        if ($parent_class_name === null) {
            $parent_class_name = $this->class->getFQCN();
        }

        return $parent_class_name . '::' . $this->getName() . '()';
    }
}