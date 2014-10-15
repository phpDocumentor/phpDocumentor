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

/**
 * Tests for the \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler class.
 */
class ExampleAssemblerTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_FILE_PATH     = 'examples/example.txt';
    const EXAMPLE_STARTING_LINE = 10;
    const EXAMPLE_LINE_COUNT    = 5;
    const EXAMPLE_DESCRIPTION   = 'This is a description';
    const EXAMPLE_TEXT          = 'This is an example';
    const TAG_NAME              = 'example';

    /** @var ExampleAssembler */
    private $fixture;

    /** @var Finder|m\MockInterface */
    private $finderMock;

    /**
     * Initializes this fixture and its dependencies.
     */
    protected function setUp()
    {
        $this->finderMock = m::mock('phpDocumentor\Descriptor\Example\Finder');
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

        $this->assertSame($descriptor->getName(), self::TAG_NAME);
        $this->assertSame($descriptor->getDescription(), self::EXAMPLE_DESCRIPTION);
        $this->assertSame($descriptor->getFilePath(), self::EXAMPLE_FILE_PATH);
        $this->assertSame($descriptor->getStartingLine(), self::EXAMPLE_STARTING_LINE);
        $this->assertSame($descriptor->getLineCount(), self::EXAMPLE_LINE_COUNT);
        $this->assertSame($descriptor->getExample(), self::EXAMPLE_TEXT);
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
        $exampleTagMock = m::mock('phpDocumentor\Reflection\DocBlock\Tag\ExampleTag');
        $exampleTagMock->shouldReceive('getName')->andReturn(self::TAG_NAME);
        $exampleTagMock->shouldReceive('getFilePath')->andReturn(self::EXAMPLE_FILE_PATH);
        $exampleTagMock->shouldReceive('getStartingLine')->andReturn(self::EXAMPLE_STARTING_LINE);
        $exampleTagMock->shouldReceive('getLineCount')->andReturn(self::EXAMPLE_LINE_COUNT);
        $exampleTagMock->shouldReceive('getDescription')->andReturn(self::EXAMPLE_DESCRIPTION);

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
