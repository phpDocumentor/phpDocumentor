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
namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Reflection\DocBlock\Tags\Example;

/**
 * Tests for the \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler class.
 */
class ExampleAssemblerTest extends MockeryTestCase
{
    public const EXAMPLE_FILE_PATH = 'examples/example.txt';

    public const EXAMPLE_STARTING_LINE = 10;

    public const EXAMPLE_LINE_COUNT = 5;

    public const EXAMPLE_DESCRIPTION = 'This is a description';

    public const EXAMPLE_TEXT = 'This is an example';

    public const TAG_NAME = 'example';

    /** @var ExampleAssembler */
    private $fixture;

    /** @var Finder|m\MockInterface */
    private $finderMock;

    /**
     * Initializes this fixture and its dependencies.
     */
    protected function setUp() : void
    {
        $this->finderMock = m::mock(ExampleFinder::class);
        $this->fixture    = new ExampleAssembler($this->finderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler::create
     */
    public function testCreateDescriptorFromExampleTag() : void
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
     */
    public function testExceptionIsThrownIfTheWrongObjectIsPassed() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->create('this is an error');
    }

    /**
     * Returns a mock Example tag that will return example data (as provided in the class constants) when asked to.
     */
    private function givenExampleTagWithTestData() : Example
    {
        return new Example(
            self::EXAMPLE_FILE_PATH,
            false,
            self::EXAMPLE_STARTING_LINE,
            self::EXAMPLE_LINE_COUNT,
            self::EXAMPLE_DESCRIPTION
        );
    }

    /**
     * Instructs the finder dependency to return the given text when an example file is to be found.
     */
    private function whenExampleFileContains(string $exampleText) : void
    {
        $this->finderMock->shouldReceive('find')->andReturn($exampleText);
    }
}
