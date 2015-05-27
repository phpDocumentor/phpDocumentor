<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes;

use Symfony\Component\Validator\Constraint;

/**
 * Validates whether a file, class, interface or trait has not more than ! package tags.
 */
class HasSinglePackage extends Constraint
{
    /** @var string message */
    public $message = 'Only one @package tag is allowed';

    public $code = 50001;

    /**
     * Returns that the constraint can be put onto classes.
     *
     * @return string
     */
    public function getTargets()
    {
        return array(self::CLASS_CONSTRAINT);
    }
}
