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

namespace phpDocumentor\Plugin\Standards\phpDocumentor\Sniff;

use Mockery as m;

/**
 * Tests for the ParamIsIdeDefault class.
 */
class ParamIsIdeDefaultTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_RULE_NAME  = 'my.rule.name';
    const EXAMPLE_DESCRIPTOR = 'File';

    /** @var m\MockInterface */
    private $validatorMock;

    /** @var ParamIsIdeDefault */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies with mocks.
     */
    protected function setUp()
    {
        $this->validatorMock = m::mock('Symfony\Component\Validator\Validator');
        $this->fixture = new ParamIsIdeDefault(
            $this->validatorMock,
            self::EXAMPLE_RULE_NAME,
            self::EXAMPLE_DESCRIPTOR
        );
    }

    /**
     * @covers phpDocumentor\Plugin\Standards\AbstractSniff::__construct
     * @covers phpDocumentor\Plugin\Standards\AbstractSniff::getName
     */
    public function testIfNameIsCorrectlyReturned()
    {
        $this->assertSame(self::EXAMPLE_RULE_NAME, $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Plugin\Standards\AbstractSniff::__construct
     * @covers phpDocumentor\Plugin\Standards\AbstractSniff::enable
     * @covers phpDocumentor\Plugin\Standards\AbstractSniff::getMetaData
     * @covers phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\ParamIsIdeDefault::getConstraint
     */
    public function testIfCorrectConstraintIsPassed()
    {
        $metadataMock = m::mock('\Symfony\Component\Validator\Mapping\ClassMetadata');
        $metadataMock
            ->shouldReceive('addConstraint')
            ->once()
            ->with(
                m::type('phpDocumentor\Plugin\Standards\phpDocumentor\Constraints\Functions\IsParamTypeNotAnIdeDefault')
            );

        $this->validatorMock
            ->shouldReceive('getMetadataFor')
            ->once()
            ->with('phpDocumentor\Descriptor\FileDescriptor')
            ->andReturn($metadataMock);

        $this->fixture->enable();

        $this->assertTrue(true);
    }
}
