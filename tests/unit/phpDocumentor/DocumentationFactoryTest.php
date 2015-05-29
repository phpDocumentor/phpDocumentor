<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Mockery as m;
use phpDocumentor\Project\Version\Definition;
use phpDocumentor\Project\VersionNumber;

/**
 * Class DocumentationFactoryTest
 * @coversDefaultClass phpDocumentor\DocumentationFactory
 */
class DocumentationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentationFactory
     */
    private $fixture;

    /**
     * @var m\Mock
     */
    private $documentGroupFactoryMock;

    protected function setUp()
    {
        $this->fixture = new DocumentationFactory();
        $this->documentGroupFactoryMock = m::mock(DocumentGroupFactory::class);
    }

    /**
     * @covers ::create
     */
    public function testMinimalDocumentationCreation()
    {
        $versionDefinition = new Definition(new VersionNumber('1.0.0'));

        $documentation = $this->fixture->create($versionDefinition);

        $this->assertEquals(new VersionNumber('1.0.0'), $documentation->getVersionNumber());
    }

    /**
     * @covers ::create
     * @covers ::<private>
     * @covers ::addDocumentGroupFactory
     */
    public function testDocumentGroupFactoryIsCalled()
    {
        $groupDefinition = new DummyDocumentGroupDefinition();
        $versionDefinition = new Definition(
            new VersionNumber('1.0.0'),
            array($groupDefinition)
        );

        $this->documentGroupFactoryMock
            ->shouldReceive('matches')
            ->once()
            ->andReturn(true);

        $this->documentGroupFactoryMock->shouldReceive('create')
            ->once()
            ->with($groupDefinition)
            ->andReturn(new DummyDocumentGroup());

        $this->fixture->addDocumentGroupFactory($this->documentGroupFactoryMock);
        $documentation = $this->fixture->create($versionDefinition);

        $this->assertEquals(new VersionNumber('1.0.0'), $documentation->getVersionNumber());
        $this->assertCount(1, $documentation->getDocumentGroups());
    }

    /**
     * @covers ::create
     * @covers ::<private>
     * @covers ::addDocumentGroupFactory
     * @expectedException phpDocumentor\FactoryNotFoundException
     */
    public function testDocumentGroupFactoryShouldHaveMatch()
    {
        $versionDefinition = new Definition(
            new VersionNumber('1.0.0'),
            array(new DummyDocumentGroupDefinition())
        );

        $this->documentGroupFactoryMock
            ->shouldReceive('matches')
            ->once()
            ->andReturn(false);

        $this->documentGroupFactoryMock->shouldReceive('create')
            ->never();

        $this->fixture->addDocumentGroupFactory($this->documentGroupFactoryMock);
        $this->fixture->create($versionDefinition);
    }

}
