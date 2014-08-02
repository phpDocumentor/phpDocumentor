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

use Mockery\MockInterface;
use Mockery as m;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\Collection;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Test class for \phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSinglePackageValidator.
 */
class HasSinglePackageValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var HasSinglePackageValidator */
    protected $validator;

    /** @var HasSinglePackage */
    protected $constraint;

    /** @var MockInterface|FileDescriptor */
    protected $fileDescriptor;

    /** @var MockInterface|ExecutionContextInterface */
    protected $context;

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    public function setUp()
    {
        $this->constraint = new HasSinglePackage();
        $this->fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $this->context = m::mock('Symfony\Component\Validator\ExecutionContextInterface');

        $this->validator = new HasSinglePackageValidator();
        $this->validator->initialize($this->context);
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @covers phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSinglePackageValidator::validate
     */
    public function testValidateWithBadInput()
    {
        $this->validator->validate(new \stdClass(), $this->constraint);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSinglePackageValidator::validate
     */
    public function testValidateHappyPath()
    {
        $packageCollection = new Collection(array('x', 'y'));
        $tagCollection     = new Collection(array('package' => $packageCollection));
        $this->fileDescriptor->shouldReceive('getTags')->andReturn($tagCollection)->once();
        $this->context->shouldReceive('addViolationAt')->once()->with('package', $this->constraint->message, array(), null, null, $this->constraint->code);

        $this->validator->validate($this->fileDescriptor, $this->constraint);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints\Classes\HasSinglePackageValidator::validate
     */
    public function testValidateSinglePackage()
    {
        $packageCollection = new Collection(array('x'));
        $tagCollection     = new Collection(array('package' => $packageCollection));
        $this->fileDescriptor->shouldReceive('getTags')->andReturn($tagCollection)->once();
        $this->context->shouldReceive('addViolationAt')->never();

        $this->validator->validate($this->fileDescriptor, $this->constraint);
    }
}
