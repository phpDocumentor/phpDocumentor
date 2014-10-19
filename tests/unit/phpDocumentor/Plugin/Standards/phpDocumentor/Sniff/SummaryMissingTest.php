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
 * Tests for the SummaryMissing class.
 */
class SummaryMissingTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_RULE_NAME  = 'my.rule.name';
    const EXAMPLE_DESCRIPTOR = 'File';

    /** @var m\MockInterface */
    private $validatorMock;

    /** @var SummaryMissing */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies with mocks.
     */
    protected function setUp()
    {
        $this->validatorMock = m::mock('Symfony\Component\Validator\Validator');
        $this->fixture = new SummaryMissing(
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
     * @covers phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\SummaryMissing::getConstraint
     */
    public function testIfCorrectConstraintIsPassed()
    {
        $metadataMock = m::mock('\Symfony\Component\Validator\Mapping\ClassMetadata');
        $metadataMock
            ->shouldReceive('addPropertyConstraint')
            ->once()
            ->with('summary', m::type('Symfony\Component\Validator\Constraints\NotBlank'));

        $this->validatorMock
            ->shouldReceive('getMetadataFor')
            ->once()
            ->with('phpDocumentor\Descriptor\FileDescriptor')
            ->andReturn($metadataMock);

        $this->fixture->enable();

        $this->assertTrue(true);
    }
}
