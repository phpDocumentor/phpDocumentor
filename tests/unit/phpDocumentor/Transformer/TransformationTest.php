<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use Mockery as m;
use phpDocumentor\Transformer\Template\Parameter;

/**
 * Test class for phpDocumentor\Transformer\Transformation
 */
class TransformationTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /** @var Transformation $fixture */
    protected $fixture;

    /** @var string */
    protected $query = 'queryString';

    /** @var string */
    protected $writer = 'writerString';

    /** @var string */
    protected $source = 'sourceString';

    /** @var string */
    protected $artifact = 'artifactString';

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    protected function setUp(): void
    {
        $this->fixture = new Transformation($this->query, $this->writer, $this->source, $this->artifact);
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getQuery
     */
    public function testGetQuery() : void
    {
        $this->assertSame($this->query, $this->fixture->getQuery());
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getWriter
     */
    public function testGetWriter() : void
    {
        $this->assertSame($this->writer, $this->fixture->getWriter());
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getSource
     */
    public function testGetSource() : void
    {
        $this->assertSame($this->source, $this->fixture->getSource());
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getArtifact
     */
    public function testGetArtifact() : void
    {
        $this->assertSame($this->artifact, $this->fixture->getArtifact());
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getParameters
     * @covers \phpDocumentor\Transformer\Transformation::setParameters
     */
    public function testSetAndGetParameters() : void
    {
        $this->assertSame([], $this->fixture->getParameters());

        $parameters = $this->givenAParameter();

        $this->assertSame($parameters, $this->fixture->getParameters());
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getParameter
     */
    public function testGetParameterWithExistingName() : void
    {
        $parameters = $this->givenAParameter();
        $this->assertSame($parameters['firstKey'], $this->fixture->getParameter('name'));
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getParameter
     */
    public function testGetParameterWithNonExistingName() : void
    {
        $this->assertNull($this->fixture->getParameter('somethingElse'));
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getParametersWithKey
     */
    public function testGetParametersWithKeyWithExistingName() : void
    {
        $parameters = $this->givenAParameter();
        $this->assertSame([$parameters['firstKey']], $this->fixture->getParametersWithKey('name'));
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getParametersWithKey
     */
    public function testGetParametersWithKeyWithNonExistingName() : void
    {
        $this->assertSame([], $this->fixture->getParametersWithKey('somethingElse'));
    }

    /**
     * @covers \phpDocumentor\Transformer\Transformation::getTransformer
     * @covers \phpDocumentor\Transformer\Transformation::setTransformer
     */
    public function testSetAndGetTransformer() : void
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformer');

        $this->assertNull($this->fixture->getTransformer());

        $this->fixture->setTransformer($transformer);

        $this->assertSame($transformer, $this->fixture->getTransformer());
    }

    /**
     * Sets a parameter in the fixture for tests that need to get parameters and
     * returns the parameter array used to set this parameter for comparison
     */
    private function givenAParameter() : array
    {
        $parameterMock = m::mock('phpDocumentor\Transformer\Template\Parameter');
        $parameterMock->shouldReceive('getKey')->andReturn('name');
        $parameters = ['firstKey' => $parameterMock];
        $this->fixture->setParameters($parameters);

        return $parameters;
    }
}
