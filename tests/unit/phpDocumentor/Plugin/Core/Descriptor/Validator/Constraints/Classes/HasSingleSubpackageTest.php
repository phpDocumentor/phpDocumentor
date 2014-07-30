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

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes;

/**
 * Test class for \phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSingleSubpackage.
 */
class HasSingleSubpackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSingleSubpackage::getTargets
     */
    public function testGetTargetsClassConstraint()
    {
        $constraint = new HasSingleSubpackage();

        $this->assertEquals(array(HasSingleSubpackage::CLASS_CONSTRAINT), $constraint->getTargets());
    }
}
