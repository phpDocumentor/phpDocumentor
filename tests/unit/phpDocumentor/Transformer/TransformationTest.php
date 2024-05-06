<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use Mockery as m;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template\Parameter;

/** @coversDefaultClass \phpDocumentor\Transformer\Transformation */
final class TransformationTest extends m\Adapter\Phpunit\MockeryTestCase
{
    use Faker;

    private Transformation $fixture;

    private string $query = 'queryString';

    private string $writer = 'writerString';

    private string $source = 'sourceString';

    private string $artifact = 'artifactString';

    /** @var Template */
    private $template;

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    protected function setUp(): void
    {
        $this->template = self::faker()->template('My Template');
        $this->fixture = new Transformation(
            $this->template,
            $this->query,
            $this->writer,
            $this->source,
            $this->artifact,
        );
    }

    public function testGetTemplate(): void
    {
        $this->assertSame($this->template, $this->fixture->template());
    }

    public function testGetQuery(): void
    {
        $this->assertSame('$.' . $this->query, $this->fixture->getQuery());
    }

    public function testGetWriter(): void
    {
        $this->assertSame($this->writer, $this->fixture->getWriter());
    }

    public function testGetSource(): void
    {
        $this->assertSame($this->source, $this->fixture->getSource());
    }

    public function testGetArtifact(): void
    {
        $this->assertSame($this->artifact, $this->fixture->getArtifact());
    }

    public function testSetAndGetParameters(): void
    {
        $this->assertSame([], $this->fixture->getParameters());

        $parameters = $this->givenAParameter();

        $this->assertSame($parameters, $this->fixture->getParameters());
    }

    public function testGetParameterWithExistingName(): void
    {
        $parameters = $this->givenAParameter();
        $this->assertSame($parameters['firstKey'], $this->fixture->getParameter('firstKey'));
    }

    public function testGetParameterWithNonExistingName(): void
    {
        $this->assertNull($this->fixture->getParameter('somethingElse'));
    }

    public function testGetParametersWithKeyWithExistingName(): void
    {
        $parameters = $this->givenAParameter();
        $this->assertEquals([$parameters['firstKey']], $this->fixture->getParametersWithKey('firstKey'));
    }

    public function testGetParametersWithKeyWithNonExistingName(): void
    {
        $parameters = $this->givenAParameter();
        $this->assertSame([], $this->fixture->getParametersWithKey('somethingElse'));
    }

    public function testSetAndGetTransformer(): void
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
    private function givenAParameter(): array
    {
        $parameters = ['firstKey' => new Parameter('firstKey', 'value')];
        $this->fixture->setParameters($parameters);

        return $parameters;
    }
}
