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

namespace phpDocumentor\Transformer;

use Mockery\MockInterface;
use Mockery as m;
use phpDocumentor\Transformer\Template\Parameter;

/**
 * Test class for phpDocumentor\Transformer\Transformation
 */
class TransformationTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $query = 'queryString';
    
    /** @var string */
    protected $writer = 'writerString';
    
    /** @var string */
    protected $source = 'sourceString';
    
    /** @var string */
    protected $artifact = 'artifactString';
    
    /** @var MockInterface|Transformer */
    protected $transformer;
    
    /** @var Parameter[] */
    protected $parameters = array();

    /** @var MockInterface|Parameter */
    protected $parameterMock;

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    public function setUp()
    {
        $this->parameterMock = m::mock('phpDocumentor\Transformer\Template\Parameter');
        $this->parameters = array("firstKey" => $this->parameterMock);
        $this->transformer = m::mock('phpDocumentor\Transformer\Transformer');
        $this->fixture = new Transformation($this->query, $this->writer, $this->source, $this->artifact);
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformer::__construct
     * @covers phpDocumentor\Transformer\Transformer::setQuery
     * @covers phpDocumentor\Transformer\Transformer::setWriter
     * @covers phpDocumentor\Transformer\Transformer::setSource
     * @covers phpDocumentor\Transformer\Transformer::setArtifact
     */
    public function testIfDependenciesAreCorrectlyRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->query, 'query', $this->fixture);
        $this->assertAttributeSame($this->writer, 'writer', $this->fixture);
        $this->assertAttributeSame($this->source, 'source', $this->fixture);
        $this->assertAttributeSame($this->artifact, 'artifact', $this->fixture);
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getQuery
     */
    public function testGetQuery()
    {
        $this->assertSame($this->query, $this->fixture->getQuery());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformation::getWriter
     */
    public function testGetWriter()
    {
        $this->assertSame($this->writer, $this->fixture->getWriter());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformation::getSource
     */
    public function testGetSource()
    {
        $this->assertSame($this->source, $this->fixture->getSource());
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getArtifact
     */
    public function testGetArtifact()
    {
        $this->assertSame($this->artifact, $this->fixture->getArtifact());
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getParameters
     * @covers phpDocumentor\Transformer\Transformation::setParameters
     */
    public function testSetAndGetParameters()
    {
        $this->assertSame(array(), $this->fixture->getParameters());
    
        $this->setParameter();
    
        $this->assertSame($this->parameters, $this->fixture->getParameters());
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getParameter
     */
    public function testGetParameterWithExistingName()
    {
        $this->setParameter();
        $this->assertSame($this->parameterMock, $this->fixture->getParameter('name'));
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getParameter
     */
    public function testGetParameterWithNonExistingName()
    {
        $this->setParameter();
        $this->assertSame(null, $this->fixture->getParameter('somethingElse'));
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getParametersWithKey
     */
    public function testGetParametersWithKeyWithExistingName()
    {
        $this->setParameter();
        $this->assertSame(array($this->parameterMock), $this->fixture->getParametersWithKey('name'));
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getParametersWithKey
     */
    public function testGetParametersWithKeyWithNonExistingName()
    {
        $this->setParameter();
        $this->assertSame(array(), $this->fixture->getParametersWithKey('somethingElse'));
    }
    
    /**
     * @covers phpDocumentor\Transformer\Transformation::getTransformer
     * @covers phpDocumentor\Transformer\Transformation::setTransformer
     */
    public function testSetAndGetTransformer()
    {
        $this->assertSame(null, $this->fixture->getTransformer());

        $this->fixture->setTransformer($this->transformer);

        $this->assertSame($this->transformer, $this->fixture->getTransformer());
    }

    /**
     * Sets a parameter for tests that need to get parameters
     */
    private function setParameter()
    {
        $this->parameterMock->shouldReceive('getKey')->andReturn('name');
        $this->fixture->setParameters($this->parameters);
    }
}
