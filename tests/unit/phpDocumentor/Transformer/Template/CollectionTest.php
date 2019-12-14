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

namespace phpDocumentor\Transformer\Template;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;
use phpDocumentor\Transformer\Writer\WriterAbstract;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Template\Collection
 * @covers ::__construct
 * @covers ::<private>
 */
final class CollectionTest extends MockeryTestCase
{
    use Faker;

    /** @var m\MockInterface|WriterCollection */
    private $writerCollectionMock;

    /** @var m\MockInterface|Factory */
    private $factoryMock;

    /** @var Collection */
    private $fixture;

    /**
     * Constructs the fixture with provided mocked dependencies.
     */
    protected function setUp() : void
    {
        $this->factoryMock = m::mock(Factory::class);
        $this->writerCollectionMock = m::mock(WriterCollection::class);
        $this->fixture = new Collection($this->factoryMock, $this->writerCollectionMock);
    }

    /**
     * @covers ::load
     */
    public function testIfLoadRetrievesTemplateFromFactoryAndRegistersIt() : void
    {
        // Arrange
        $templateName = 'default';
        $template = $this->faker()->template($templateName);
        $template['a'] = $this->givenAnEmptyTransformation($template);

        $transformer = $this->faker()->transformer();
        $this->factoryMock->shouldReceive('get')->with($transformer, $templateName)->andReturn($template);

        $writer = m::mock(WriterAbstract::class);
        $writer->shouldReceive('checkRequirements')->andReturnNull();

        $this->writerCollectionMock
            ->shouldReceive('offsetGet')->with('')
            ->andReturn($writer);

        // Act
        $this->fixture->load($transformer, $templateName);

        // Assert
        $this->assertCount(1, $this->fixture);
        $this->assertArrayHasKey($templateName, $this->fixture);
        $this->assertSame($template, $this->fixture[$templateName]);
    }

    /**
     * @covers ::getTemplatesPath
     */
    public function testCollectionProvidesTemplatesPath() : void
    {
        // Arrange
        $path = '/tmp';
        $this->factoryMock->shouldReceive('getTemplatesPath')->andReturn($path);

        // Act
        $result = $this->fixture->getTemplatesPath();

        // Assert
        $this->assertSame($path, $result);
    }

    /**
     * @covers ::getTransformations
     */
    public function testIfAllTransformationsCanBeRetrieved() : void
    {
        // Arrange
        $transformation1 = $this->givenAnEmptyTransformation();
        $transformation2 = $this->givenAnEmptyTransformation();
        $transformation3 = $this->givenAnEmptyTransformation();
        $this->whenThereIsATemplateWithNameAndTransformations(
            'template1',
            ['a' => $transformation1, 'b' => $transformation2]
        );
        $this->whenThereIsATemplateWithNameAndTransformations('template2', ['c' => $transformation3]);

        // Act
        $result = $this->fixture->getTransformations();

        // Assert
        $this->assertCount(3, $result);
        $this->assertSame([$transformation1, $transformation2, $transformation3], $result);
    }

    /**
     * Returns a transformation object without information in it.
     */
    private function givenAnEmptyTransformation(?Template $template = null) : Transformation
    {
        return $this->faker()->transformation($template);
    }

    /**
     * Adds a template to the fixture with the given name and transformations.
     *
     * @param Transformation[] $transformations
     */
    private function whenThereIsATemplateWithNameAndTransformations(string $name, array $transformations) : void
    {
        $template = $this->faker()->template($name);
        foreach ($transformations as $key => $transformation) {
            $template[$key] = $transformation;
        }

        $this->fixture[$name] = $template;
    }
}
