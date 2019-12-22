<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use Mockery as m;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template\Parameter;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Transformation
 * @covers ::__construct
 * @covers ::<private>
 */
final class TransformationTest extends m\Adapter\Phpunit\MockeryTestCase
{
    use Faker;

    /** @var Transformation $fixture */
    private $fixture;

    /** @var string */
    private $query = 'queryString';

    /** @var string */
    private $writer = 'writerString';

    /** @var string */
    private $source = 'sourceString';

    /** @var string */
    private $artifact = 'artifactString';

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    protected function setUp() : void
    {
        $this->template = new Template('My Template', $this->faker()->fileSystem());
        $this->fixture = new Transformation(
            $this->template,
            $this->query,
            $this->writer,
            $this->source,
            $this->artifact
        );
    }

    /**
     * @covers ::getQuery
     */
    public function testGetQuery() : void
    {
        $this->assertSame($this->query, $this->fixture->getQuery());
    }

    /**
     * @covers ::getWriter
     */
    public function testGetWriter() : void
    {
        $this->assertSame($this->writer, $this->fixture->getWriter());
    }

    /**
     * @covers ::getSource
     */
    public function testGetSource() : void
    {
        $this->assertSame($this->source, $this->fixture->getSource());
    }

    /**
     * @covers ::getArtifact
     */
    public function testGetArtifact() : void
    {
        $this->assertSame($this->artifact, $this->fixture->getArtifact());
    }

    /**
     * @covers ::getParameters
     * @covers ::setParameters
     */
    public function testSetAndGetParameters() : void
    {
        $this->assertSame([], $this->fixture->getParameters());

        $parameters = $this->givenAParameter();

        $this->assertSame($parameters, $this->fixture->getParameters());
    }

    /**
     * @covers ::getParameter
     */
    public function testGetParameterWithExistingName() : void
    {
        $parameters = $this->givenAParameter();
        $this->assertSame($parameters['firstKey'], $this->fixture->getParameter('firstKey'));
    }

    /**
     * @covers ::getParameter
     */
    public function testGetParameterWithNonExistingName() : void
    {
        $this->assertNull($this->fixture->getParameter('somethingElse'));
    }

    /**
     * @covers ::getParametersWithKey
     */
    public function testGetParametersWithKeyWithExistingName() : void
    {
        $parameters = $this->givenAParameter();
        $this->assertEquals([$parameters['firstKey']], $this->fixture->getParametersWithKey('firstKey'));
    }

    /**
     * @covers ::getParametersWithKey
     */
    public function testGetParametersWithKeyWithNonExistingName() : void
    {
        $this->assertSame([], $this->fixture->getParametersWithKey('somethingElse'));
    }

    /**
     * @covers ::getTransformer
     * @covers ::setTransformer
     */
    public function testSetAndGetTransformer() : void
    {
        $transformer = m::mock(Transformer::class);

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
        $parameters = ['firstKey' => new Parameter('firstKey', 'value')];
        $this->fixture->setParameters($parameters);

        return $parameters;
    }
}
