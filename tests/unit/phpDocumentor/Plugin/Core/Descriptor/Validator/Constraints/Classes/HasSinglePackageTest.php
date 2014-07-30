<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes;

/**
 * Test class for \phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSinglePackage.
 */
class HasSinglePackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSinglePackage::getTargets
     */
    public function testGetTargetsClassConstraint()
    {
        $constraint = new HasSinglePackage();

        $this->assertEquals(array(HasSinglePackage::CLASS_CONSTRAINT), $constraint->getTargets());
    }
}
