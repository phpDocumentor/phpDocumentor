<?php

namespace phpDocumentor\Partials;

use Mockery as m;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection */
    protected $fixture;

    /** @var m\MockInterface|\Parsedown */
    protected $parser;

    /**
     * Constructs the fixture and adds mocked dependencies.
     */
    protected function setUp()
    {
        $this->parser = m::mock('\Parsedown');
        $this->fixture = new Collection($this->parser);
    }

    /**
     * @covers phpDocumentor\Partials\Collection::__construct
     */
    public function testIfParserIsRegisteredWithCollectionUponInstantiation()
    {
        $this->assertAttributeSame($this->parser, 'parser', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Partials\Collection::set
     */
    public function testProvidedPartialIsConvertedIntoHTMLWhenSettingIt()
    {
        // Arrange
        $input  = 'This is a *Partial* text';
        $output = 'This is a <em>Partial</em> text';
        $this->parser->shouldReceive('text')->with($input)->andReturn($output);

        // Act
        $this->fixture->set('test', $input);

        // Assert
        $this->assertSame($output, $this->fixture['test']);
    }
}
