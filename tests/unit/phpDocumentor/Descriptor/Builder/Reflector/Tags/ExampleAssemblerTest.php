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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Reflection\DocBlock\Tags\Example;

/**
 * Tests for the \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler class.
 */
class ExampleAssemblerTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_FILE_PATH = 'examples/example.txt';
    const EXAMPLE_STARTING_LINE = 10;
    const EXAMPLE_LINE_COUNT = 5;
    const EXAMPLE_DESCRIPTION = 'This is a description';
    const EXAMPLE_TEXT = 'This is an example';
    const TAG_NAME = 'example';

    /** @var ExampleAssembler */
    private $fixture;

    /** @var Finder|m\MockInterface */
    private $finderMock;

    /**
     * Initializes this fixture and its dependencies.
     */
    protected function setUp()
    {
        $this->finderMock = m::mock(ExampleFinder::class);
        $this->fixture = new ExampleAssembler($this->finderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler::create
     */
    public function testCreateDescriptorFromExampleTag()
    {
        $exampleTagMock = $this->givenExampleTagWithTestData();
        $this->whenExampleFileContains(self::EXAMPLE_TEXT);

        $descriptor = $this->fixture->create($exampleTagMock);

        $this->assertSame(self::TAG_NAME, $descriptor->getName());
        $this->assertSame(self::EXAMPLE_DESCRIPTION, $descriptor->getDescription());
        $this->assertSame(self::EXAMPLE_FILE_PATH, $descriptor->getFilePath());
        $this->assertSame(self::EXAMPLE_STARTING_LINE, $descriptor->getStartingLine());
        $this->assertSame(self::EXAMPLE_LINE_COUNT, $descriptor->getLineCount());
        $this->assertSame(self::EXAMPLE_TEXT, $descriptor->getExample());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler::create
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIsThrownIfTheWrongObjectIsPassed()
    {
        $this->fixture->create('this is an error');
    }

    /**
     * Returns a mock Example tag that will return example data (as provided in the class constants) when asked to.
     *
     * @return m\MockInterface
     */
    private function givenExampleTagWithTestData()
    {
        $exampleTagMock = new Example(
            self::EXAMPLE_FILE_PATH,
            false,
            self::EXAMPLE_STARTING_LINE,
            self::EXAMPLE_LINE_COUNT,
            self::EXAMPLE_DESCRIPTION
        );

        return $exampleTagMock;
    }

    /**
     * Instructs the finder dependency to return the given text when an example file is to be found.
     *
     * @param string $exampleText
     *
     * @return void
     */
    private function whenExampleFileContains($exampleText)
    {
        $this->finderMock->shouldReceive('find')->andReturn($exampleText);
    }
}
