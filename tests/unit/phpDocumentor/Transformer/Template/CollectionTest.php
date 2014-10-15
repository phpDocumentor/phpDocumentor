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

namespace phpDocumentor\Transformer\Template;

use Mockery as m;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var m\MockInterface|\phpDocumentor\Transformer\Writer\Collection */
    private $writerCollectionMock;

    /** @var m\MockInterface|Factory */
    private $factoryMock;

    /** @var Collection */
    private $fixture;

    /**
     * Constructs the fixture with provided mocked dependencies.
     */
    public function setUp()
    {
        $this->factoryMock          = m::mock('phpDocumentor\Transformer\Template\Factory');
        $this->writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $this->fixture              = new Collection($this->factoryMock, $this->writerCollectionMock);
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\Collection::__construct
     */
    public function testIfDependenciesAreRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->factoryMock, 'factory', $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\Collection::load
     */
    public function testIfLoadRetrievesTemplateFromFactoryAndRegistersIt()
    {
        // Arrange
        $templateName = 'default';
        $template     = new Template($templateName);
        $this->factoryMock->shouldReceive('get')->with($templateName)->andReturn($template);

        // Act
        $this->fixture->load($templateName);

        // Assert
        $this->assertCount(1, $this->fixture);
        $this->assertTrue(isset($this->fixture[$templateName]));
        $this->assertSame($template, $this->fixture[$templateName]);
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\Collection::getTemplatesPath
     */
    public function testCollectionProvidesTemplatePath()
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
    public function testIfAllTransformationsCanBeRetrieved()
    {
        // Arrange
        $transformation1 = $this->givenAnEmptyTransformation();
        $transformation2 = $this->givenAnEmptyTransformation();
        $transformation3 = $this->givenAnEmptyTransformation();
        $this->whenThereIsATemplateWithNameAndTransformations('template1', array($transformation1, $transformation2));
        $this->whenThereIsATemplateWithNameAndTransformations('template2', array($transformation3));

        // Act
        $result = $this->fixture->getTransformations();

        // Assert
        $this->assertCount(3, $result);
        $this->assertSame(array($transformation1, $transformation2, $transformation3), $result);
    }

    /**
     * Returns a transformation object without information in it.
     *
     * @return Transformation
     */
    protected function givenAnEmptyTransformation()
    {
        return new Transformation('', '', '', '');
    }

    /**
     * Adds a template to the fixture with the given name and transformations.
     *
     * @param string           $name
     * @param Transformation[] $transformations
     *
     * @return void
     */
    protected function whenThereIsATemplateWithNameAndTransformations($name, array $transformations)
    {
        $template = new Template($name);
        foreach ($transformations as $transformation) {
            $template[] = $transformation;
        }
        $this->fixture[$name] = $template;
    }
}
