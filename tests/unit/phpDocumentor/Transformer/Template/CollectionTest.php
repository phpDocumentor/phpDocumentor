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
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;

final class CollectionTest extends MockeryTestCase
{
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
     * @covers \phpDocumentor\Transformer\Template\Collection::load
     */
    public function testIfLoadRetrievesTemplateFromFactoryAndRegistersIt() : void
    {
        // Arrange
        $templateName = 'default';
        $template = new Template($templateName);
        $this->factoryMock->shouldReceive('get')->with($templateName)->andReturn($template);

        // Act
        $this->fixture->load($templateName);

        // Assert
        $this->assertCount(1, $this->fixture);
        $this->assertArrayHasKey($templateName, $this->fixture);
        $this->assertSame($template, $this->fixture[$templateName]);
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\Collection::getTemplatesPath
     */
    public function testCollectionProvidesTemplatePath() : void
    {
        // Arrange
        $path = '/tmp';
        $this->factoryMock->shouldReceive('getTemplatePath')->andReturn($path);

        // Act
        $result = $this->fixture->getTemplatesPath();

        // Assert
        $this->assertSame($path, $result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\Collection::getTransformations
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
    protected function givenAnEmptyTransformation() : Transformation
    {
        return new Transformation('', '', '', '');
    }

    /**
     * Adds a template to the fixture with the given name and transformations.
     *
     * @param Transformation[] $transformations
     */
    protected function whenThereIsATemplateWithNameAndTransformations(string $name, array $transformations) : void
    {
        $template = new Template($name);
        foreach ($transformations as $key => $transformation) {
            $template[$key] = $transformation;
        }

        $this->fixture[$name] = $template;
    }
}
